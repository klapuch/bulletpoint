<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Characterice\Sql\Clause;
use Characterice\Sql\Statement\Insert;
use Klapuch\Http;
use Klapuch\Scheduling;
use Klapuch\Storage;
use Klapuch\Uri;

final class PingReferences implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function fulfill(): void {
		$references = (new Storage\TypedQuery($this->connection, 'SELECT ids, url FROM references_to_ping'))->rows();
		(new Storage\Transaction($this->connection))->start(function () use ($references): void {
			foreach ($references as $reference) {
				['ids' => $ids, 'url' => $url] = $reference;
				(new Storage\BuiltQuery(
					$this->connection,
					(new Insert\Query())
						->insertInto(new Clause\MultiInsertInto('source_pings', [
							'reference_id' => $ids,
							'status' => array_fill(0, count($ids), $this->code(new Uri\ValidUrl($url))),
						])),
				))->execute();
			}
		});
	}

	private function code(Uri\Uri $uri): ?int {
		try {
			return (new Http\BasicRequest(
				'GET',
				$uri,
				[
					CURLOPT_TIMEOUT => 2,
					CURLOPT_CONNECTTIMEOUT => 2,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_MAXREDIRS => 10,
				],
			))->send()->code();
		} catch (\Throwable $e) {
			return null;
		}
	}

	public function name(): string {
		return 'PingReferences';
	}
}
