<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Bulletpoint\Scheduling\Http\UserAgents;
use Klapuch\Http;
use Klapuch\Scheduling;
use Klapuch\Sql\Clause;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Storage;
use Klapuch\Uri;
use Tracy;

final class PingReferences implements Scheduling\Job {
	private Storage\Connection $connection;
	private Tracy\ILogger $logger;

	public function __construct(Storage\Connection $connection, Tracy\ILogger $logger) {
		$this->connection = $connection;
		$this->logger = $logger;
	}

	public function fulfill(): void {
		$references = (new Storage\TypedQuery($this->connection, 'SELECT ids, url FROM references_to_ping'))->rows();
		(new Storage\Transaction($this->connection))->start(function () use ($references): void {
			foreach ($references as $reference) {
				['ids' => $ids, 'url' => $url] = $reference;
				(new Storage\BuiltQuery(
					$this->connection,
					(new Insert\Query())
						->insertInto(new Clause\MultiInsertInto('reference_pings', [
							'reference_id' => $ids,
							'status' => array_fill(0, count($ids), $this->code(new Uri\FakeUri($url))),
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
					CURLOPT_HTTPHEADER => [sprintf('User-Agent: %s', (new UserAgents())->random())],
				],
			))->send()->code();
		} catch (\Throwable $e) {
			$this->logger->log($e);
			\Sentry\captureException($e);
			return null;
		}
	}

	public function name(): string {
		return 'PingReferences';
	}
}
