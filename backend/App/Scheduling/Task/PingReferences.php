<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Http;
use Klapuch\Scheduling;
use Klapuch\Sql\PgMultiInsertInto;
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
					(new PgMultiInsertInto(
						'reference_pings',
						[
							'reference_id' => array_fill(0, count($ids), '?'),
							'status' => array_fill(0, count($ids), '?'),
						],
						array_merge(...array_map(null, $ids, array_fill(0, count($ids), $this->code(new Uri\ValidUrl($url))))),
					)),
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