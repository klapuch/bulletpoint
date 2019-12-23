<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;

final class RunFunction implements Scheduling\Job {
	private Storage\Connection $connection;

	private string $function;

	public function __construct(Storage\Connection $connection, string $function) {
		$this->connection = $connection;
		$this->function = $function;
	}

	public function fulfill(): void {
		(new Storage\NativeQuery(
			$this->connection,
			sprintf('SELECT %s()', $this->function),
		))->execute();
	}

	public function name(): string {
		return sprintf('RunFunction - %s', $this->function);
	}
}
