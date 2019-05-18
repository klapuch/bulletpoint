<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Characterice\Sql\Clause;
use Characterice\Sql\Expression;
use Characterice\Sql\Statement\Insert;
use Characterice\Sql\Statement\Select;
use Klapuch\Storage;

final class StoredTags implements Tags {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function all(): array {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['id', 'name']))
				->from(new Expression\From(['tags']))
				->orderBy(new Expression\OrderBy(['id' => 'ASC'])),
		))->rows();
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param string $name
	 */
	public function add(string $name): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(new Clause\InsertInto('tags', ['name' => $name])),
		))->execute();
	}
}
