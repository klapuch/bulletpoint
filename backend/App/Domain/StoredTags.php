<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
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
				->orderBy(new Expression\OrderBy(['name' => 'ASC', 'id' => 'DESC'])),
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
