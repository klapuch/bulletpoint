<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use Klapuch\Storage;
use Tester\Assert;

final class TableCount implements Assertion {
	private Storage\Connection $connection;

	private string $table;

	private int $count;

	public function __construct(Storage\Connection $connection, string $table, int $count) {
		$this->connection = $connection;
		$this->table = $table;
		$this->count = $count;
	}

	public function assert(): void {
		Assert::same(
			$this->count,
			(new Storage\NativeQuery(
				$this->connection,
				sprintf('SELECT count(*) FROM "%s"', $this->table),
			))->field(),
			sprintf('%s TABLE', strtoupper($this->table)),
		);
	}
}
