<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema;

use Characterice\Sql\Clause;
use Characterice\Sql\Statement\Insert;
use Characterice\Sql\Statement\Update;
use Characterice\Sql\Statement\Delete;
use Characterice\Sql\Statement\Select;
use Characterice\Sql\Expression;
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
			(new Select\Query())
				->select(new Expression\Select(['id', 'name']))
				->from(new Expression\From([$this->table]))
				->orderBy(new Expression\OrderBy(['id' => 'ASC'])),
		))->rows();
		return (array) array_combine(array_column($enum, 'id'), $enum);
	}

	public function add(string $name): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(new Clause\InsertInto($this->table, ['name' => $name]))
		))->execute();
	}
}
