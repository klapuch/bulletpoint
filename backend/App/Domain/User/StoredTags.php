<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\User;

use Bulletpoint\Domain\Access;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredTags implements Tags {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	public function all(): array {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect(['id', 'name', 'reputation', 'rank']))
				->from(['user_tag_rank_reputations'])
				->where('user_id = :user_id', ['user_id' => $this->user->id()])
				->orderBy(['rank' => 'ASC', 'reputation' => 'DESC']),
		))->rows();
	}
}
