<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;
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

	public function create(array $theme): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'public_themes',
				[
					'name' => ':name',
					'tags' => ':tags',
					'reference_url' => ':reference_url',
					'user_id' => ':user_id',
				],
				[
					'name' => $theme['name'],
					'tags' => json_encode($theme['tags']), // TODO: use array
					'user_id' => $this->user->id(),
					'reference_url' => $theme['reference']['url'],
				]
			))->returning(['id'])
		))->field();
	}

	public function all(Dataset\Selection $selection): \Iterator {
		$themes = (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect([
					'id',
					'name',
					'tags',
					'reference_url',
					'user_id',
					'created_at',
				]))->from(['public_themes']),
				$selection
			)
		))->rows();
		foreach ($themes as $theme) {
			yield new StoredTheme(
				$theme['id'],
				new Storage\MemoryConnection($this->connection, $theme)
			);
		}
	}

	public function count(Dataset\Selection $selection): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect(['count(*)']))->from(['public_themes']),
				$selection
			)
		))->field();
	}
}
