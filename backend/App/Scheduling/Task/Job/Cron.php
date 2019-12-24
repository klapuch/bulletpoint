<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task\Job;

use Bulletpoint\Scheduling\Task;
use Klapuch\Scheduling;
use Klapuch\Storage;
use Tracy;

final class Cron implements Scheduling\Job {
	private Storage\Connection $connection;
	private Tracy\ILogger $logger;
	private \DateTimeInterface $now;

	public function __construct(Storage\Connection $connection, Tracy\ILogger $logger) {
		$this->connection = $connection;
		$this->logger = $logger;
		$this->now = new \DateTimeImmutable();
	}

	public function fulfill(): void {
		(new Scheduling\SerialJobs(
			new Scheduling\RepeatedJob(
				new Scheduling\MarkedJob(
					new Task\RefreshMaterializedView(
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
					new Task\RefreshMaterializedView(
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
					new Task\RefreshMaterializedView(
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
					new DeleteTrashFiles($this->connection),
					$this->connection,
				),
				'PT10M',
				$this->connection,
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new PingSources($this->connection, $this->logger),
					$this->connection,
				),
				fn (): bool => in_array($this->now->format('H:i'), ['02:00', '14:00'], true),
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new Task\RefreshMaterializedView(
						$this->connection,
						'broken_sources',
					),
					$this->connection,
				),
				fn (): bool => in_array($this->now->format('H:i'), ['04:00', '16:00'], true),
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new PingReferences($this->connection, $this->logger),
					$this->connection,
				),
				fn (): bool => in_array($this->now->format('H:i'), ['07:00', '19:00'], true),
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new Task\RefreshMaterializedView(
						$this->connection,
						'broken_references',
					),
					$this->connection,
				),
				fn (): bool => in_array($this->now->format('H:i'), ['09:00', '21:00'], true),
			),
			new Scheduling\CustomTriggeredJob(
				new Scheduling\MarkedJob(
					new Task\RunFunction($this->connection, 'refresh_bulletpoint_group_successors'),
					$this->connection,
				),
				fn (): bool => $this->now->format('H:i') === '00:01',
			),
		))->fulfill();
	}

	public function name(): string {
		return 'Cron';
	}
}
