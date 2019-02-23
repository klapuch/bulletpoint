<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;

final class Cron implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function fulfill(): void {
		(new Scheduling\SerialJobs(
			new Scheduling\RepeatedJob(
				new Scheduling\MarkedJob(
					new RefreshMaterializedView(
						$this->connection,
						'bulletpoint_reputations',
					),
					$this->connection,
				),
				'PT1H',
				$this->connection,
			),
			new Scheduling\RepeatedJob(
				new Scheduling\MarkedJob(
					new RefreshMaterializedView(
						$this->connection,
						'starred_themes',
					),
					$this->connection,
				),
				'PT1H',
				$this->connection,
			),
		))->fulfill();
	}

	public function name(): string {
		return 'Cron';
	}
}