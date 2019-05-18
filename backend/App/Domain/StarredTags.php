<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Characterice\Sql\Expression;
use Characterice\Sql\Statement\Select;
use Klapuch\Storage;

final class StarredTags implements Tags {
	/** @var \Bulletpoint\Domain\Tags */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Tags $origin, Storage\Connection $connection, Access\User $user) {
		$this->origin = $origin;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function all(): array {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select(['id', 'name']))
				->where(new Expression\Where('user_id', $this->user->id()))
				->orderBy(new Expression\OrderBy(['name' => 'ASC'])),
		))->rows();
	}

	public function add(string $name): void {
		$this->origin->add($name);
	}
}
