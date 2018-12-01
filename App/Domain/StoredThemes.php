<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
use Klapuch\Storage;

final class StoredThemes implements Themes {
	/** @var Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
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
				],
				[
					'name' => $theme['name'],
					'tags' => json_encode($theme['tags']), // TODO: use array
					'reference_name' => $theme['reference']['name'],
					'reference_url' => $theme['reference']['url'],
				]
			))
		))->execute();
	}
}