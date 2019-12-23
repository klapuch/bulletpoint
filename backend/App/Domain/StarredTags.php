<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Select;
use Klapuch\Storage;

final class StarredTags implements Tags {
	private Tags $origin;
	private Storage\Connection $connection;
	private Access\User $user;

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
				->from(new Expression\From(['web.starred_tags']))
				->where(new Expression\Where('user_id', $this->user->id()))
				->orderBy(new Expression\OrderBy(['name' => 'ASC'])),
		))->rows();
	}

	public function add(string $name): void {
		$this->origin->add($name);
	}
}
