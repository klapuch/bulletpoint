<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema;

use Klapuch\Storage;

final class PostgresConstant implements Enum {
	private string $name;

	private Storage\Connection $connection;

	public function __construct(string $name, Storage\Connection $connection) {
		$this->name = $name;
		$this->connection = $connection;
	}

	public function values(): array {
		return array_column(
			(new Storage\NativeQuery(
				$this->connection,
				sprintf('SELECT unnest(constant.%s()) AS values ORDER BY 1', $this->name),
			))->rows(),
			'values',
		);
	}

	public function add(string $name): void {
	}
}
