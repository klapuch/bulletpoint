<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Output;
use Klapuch\Sql;
use Klapuch\Storage;

final class StoredTheme implements Theme {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
	}

	public function print(Output\Format $format): Output\Format {
		$row = (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect([
				'id',
				'name',
				'tags',
				'reference_url',
				'user_id',
				'created_at',
			]))->from(['public_themes'])
				->where('id = :id', ['id' => $this->id])
		))->row();
		return new Output\FilledFormat(
			$format,
			[
				'id' => $row['id'],
				'name' => $row['name'],
				'tags' => $row['tags'],
				'user_id' => $row['user_id'],
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
				new Sql\AnsiUpdate('public_themes'),
			))->set([
				'name' => $theme['name'],
				'tags' => json_encode($theme['tags']), // TODO: use array
				'reference_url' => $theme['reference']['url'],
			])->where('id = :id', ['id' => $this->id])
		))->execute();
	}
}
