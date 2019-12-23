<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

final class UniqueTags implements Tags {
	private Tags $origin;
	private Storage\Connection $connection;

	public function __construct(Tags $origin, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
	}

	public function all(): array {
		return $this->origin->all();
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param string $name
	 */
	public function add(string $name): void {
		if ($this->exists($name)) {
			throw new \UnexpectedValueException(t('tag.already.exists', $name));
		}
		$this->origin->add($name);
	}

	private function exists(string $name): bool {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->from(new Expression\From(['tags']))
				->where(new Expression\Where('name', $name))
				->exists(),
		))->field();
	}
}
