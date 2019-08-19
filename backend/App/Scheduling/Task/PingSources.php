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

final class PingSources implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Tracy\ILogger */
	private $logger;

	public function __construct(Storage\Connection $connection, Tracy\ILogger $logger) {
		$this->connection = $connection;
		$this->logger = $logger;
	}

	public function fulfill(): void {
		$sources = (new Storage\TypedQuery($this->connection, 'SELECT ids, link FROM sources_to_ping'))->rows();
		(new Storage\Transaction($this->connection))->start(function () use ($sources): void {
			foreach ($sources as $source) {
				['ids' => $ids, 'link' => $link] = $source;
				(new Storage\BuiltQuery(
					$this->connection,
					(new Insert\Query())
						->insertInto(new Clause\MultiInsertInto('source_pings', [
							'source_id' => $ids,
							'status' => array_fill(0, count($ids), $this->code(new Uri\ValidUrl($link))),
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
		return 'PingSources';
	}
}
