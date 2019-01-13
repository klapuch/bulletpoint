<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Bulletpoint\Domain\Access;
use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredTheme implements Theme {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var Access\User */
	private $user;

	public function __construct(int $id, Storage\Connection $connection, Access\User $user) {
		$this->id = $id;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'name',
				'alternative_names',
				'tags',
				'reference_url',
				'user_id',
				'created_at',
				'is_starred',
			]))->from(['web.themes'])
				->where('id = :id', ['id' => $this->id])
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'name' => $row['name'],
				'alternative_names' => $row['alternative_names'],
				'tags' => $row['tags'],
				'user_id' => $row['user_id'],
				'is_starred' => $row['is_starred'],
				'created_at' => $row['created_at'],
				'reference' => [
					'url' => $row['reference_url'],
				],
			]
		);
	}

	public function change(array $theme): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PreparedUpdate(
				new Sql\AnsiUpdate('web.themes'),
			))->set([
				'name' => $theme['name'],
				'alternative_names' => json_encode($theme['alternative_names']),
				'tags' => json_encode($theme['tags']), // TODO: use array
				'reference_url' => $theme['reference']['url'],
			])->where('id = :id', ['id' => $this->id])
		))->execute();
	}

	public function star(): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'starred_themes',
				['user_id' => ':user_id', 'theme_id' => ':theme_id'],
				['user_id' => $this->user->id(), 'theme_id' => $this->id]
			))->onConflict(['user_id', 'theme_id'])->doNothing()
		))->execute();
	}

	public function unstar(): void {
		(new Storage\TypedQuery(
			$this->connection,
			'DELETE FROM starred_themes WHERE theme_id = :theme_id AND user_id = :user_id',
			['theme_id' => $this->id, 'user_id' => $this->user->id()]
		))->execute();
	}
}
