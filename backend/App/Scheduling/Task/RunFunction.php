<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Klapuch\Scheduling;
use Klapuch\Storage;

final class RunFunction implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var string */
	private $function;

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
