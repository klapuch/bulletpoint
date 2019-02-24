<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema;

use Klapuch\Sql;
use Klapuch\Storage;

final class TableEnum implements Enum {
	/** @var string */
	private $table;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(string $table, Storage\Connection $connection) {
		$this->table = $table;
		$this->connection = $connection;
	}

	public function values(): array {
		$enum = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect(['id', 'name']))
				->from([$this->table])
				->orderBy(['id' => 'ASC']),
		))->rows();
		return (array) array_combine(array_column($enum, 'id'), $enum);
	}

	public function add(string $name): void {
		(new Storage\BuiltQuery(
			$this->connection,
			new Sql\PgInsertInto($this->table, ['name' => ':name'], ['name' => $name]),
		))->execute();
	}
}
