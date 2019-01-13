<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;

final class RefreshMaterializedView implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var string */
	private $view;

	public function __construct(Storage\Connection $connection, string $view) {
		$this->connection = $connection;
		$this->view = $view;
	}

	public function fulfill(): void {
		(new Storage\NativeQuery(
			$this->connection,
			sprintf('REFRESH MATERIALIZED VIEW CONCURRENTLY %s', $this->view)
		))->execute();
	}

	public function name(): string {
		return sprintf('RefreshMaterializedView - %s', $this->view);
	}
}
