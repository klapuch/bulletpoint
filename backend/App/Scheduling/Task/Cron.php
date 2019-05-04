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
			new Scheduling\RepeatedJob(
				new Scheduling\MarkedJob(
					new RefreshMaterializedView(
						$this->connection,
						'user_tag_rank_reputations',
					),
					$this->connection,
				),
				'PT1H',
				$this->connection,
			),
			new Scheduling\RepeatedJob(
				new Scheduling\MarkedJob(
					new RemoveTrashFiles($this->connection),
					$this->connection,
				),
				'PT10M',
				$this->connection,
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new PingSources($this->connection),
					$this->connection,
				),
				static function (): bool {
					return in_array(date('H:i'), ['02:00', '14:00'], true);
				},
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new RefreshMaterializedView(
						$this->connection,
						'broken_sources',
					),
					$this->connection,
				),
				static function (): bool {
					return in_array(date('H:i'), ['04:00', '16:00'], true);
				},
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new PingReferences($this->connection),
					$this->connection,
				),
				static function (): bool {
					return in_array(date('H:i'), ['07:00', '19:00'], true);
				},
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new RefreshMaterializedView(
						$this->connection,
						'broken_references',
					),
					$this->connection,
				),
				static function (): bool {
					return in_array(date('H:i'), ['09:00', '21:00'], true);
				},
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new RunFunction($this->connection, 'refresh_bulletpoint_group_successors'),
					$this->connection,
				),
				static function (): bool {
					return date('H:i') === '00:01';
				},
			),
		))->fulfill();
	}

	public function name(): string {
		return 'Cron';
	}
}
