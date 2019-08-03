<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql\Clause;
use Klapuch\Sql\Expression;
use Klapuch\Sql\Statement\Delete;
use Klapuch\Sql\Statement\Insert;
use Klapuch\Sql\Statement\Select;
use Klapuch\Sql\Statement\Update;
use Klapuch\Storage;
use Nette\Utils\Json;

final class StoredTheme implements Theme {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(int $id, Storage\Connection $connection, Access\User $user) {
		$this->id = $id;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Select\Query())
				->select(new Expression\Select([
					'id',
					'name',
					'alternative_names',
					'tags',
					'reference_url',
					'reference_is_broken',
					'user_id',
					'created_at',
					'is_starred',
					'starred_at',
					'related_themes_id',
					'is_empty',
				]))->from(new Expression\From(['web.themes']))
				->where(new Expression\Where('id', $this->id)),
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
				'starred_at' => $row['starred_at'],
				'related_themes_id' => $row['related_themes_id'],
				'created_at' => $row['created_at'],
				'is_empty' => $row['is_empty'],
				'reference' => [
					'url' => $row['reference_url'],
					'is_broken' => $row['reference_is_broken'],
				],
			],
		);
	}

	public function change(array $theme): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Update\Query())
				->update('web.themes')
				->set(new Expression\Set(['name' => $theme['name']]))
				->set(new Expression\Set(['alternative_names' => Json::encode($theme['alternative_names'])]))
				->set(new Expression\Set(['tags' => Json::encode($theme['tags'])]))
				->set(new Expression\Set(['reference_url' => $theme['reference']['url']]))
				->where(new Expression\Where('id', $this->id)),
		))->execute();
	}

	public function star(): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Insert\Query())
				->insertInto(new Clause\InsertInto('user_starred_themes', ['user_id' => $this->user->id(), 'theme_id' => $this->id]))
				->onConflict(new Clause\OnConflict(['user_id', 'theme_id']))
				->doNothing(),
		))->execute();
	}

	public function unstar(): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Delete\Query())
				->from('user_starred_themes')
				->where(new Expression\Where('theme_id', $this->id))
				->where(new Expression\Where('user_id', $this->user->id())),
		))->execute();
	}
}
