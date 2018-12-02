<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
use Klapuch\Storage;

final class StoredThemes implements Themes {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\User */
	private $user;

	public function __construct(User $user, Storage\Connection $connection) {
		$this->connection = $connection;
		$this->user = $user;
	}

	public function create(array $theme): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'public_themes',
				[
					'name' => ':name',
					'tags' => ':tags',
					'reference_name' => ':reference_name',
					'reference_url' => ':reference_url',
					'user_id' => ':user_id',
				],
				[
					'name' => $theme['name'],
					'tags' => json_encode($theme['tags']), // TODO: use array
					'user_id' => $this->user->id(),
					'reference_name' => $theme['reference']['name'],
					'reference_url' => $theme['reference']['url'],
				]
			))
		))->execute();
	}
}
