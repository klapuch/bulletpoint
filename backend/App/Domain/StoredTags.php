<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
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
			(new Sql\AnsiSelect(['id', 'name']))
				->from(['tags'])
				->orderBy(['id' => 'ASC']),
		))->rows();
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param string $name
	 */
	public function add(string $name): void {
		(new Storage\BuiltQuery(
			$this->connection,
			new Sql\PgInsertInto('tags', ['name' => ':name'], ['name' => $name]),
		))->execute();
	}
}
