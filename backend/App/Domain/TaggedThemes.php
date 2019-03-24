<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;
use Klapuch\Sql;
use Klapuch\Storage;

final class TaggedThemes implements Themes {
	/** @var \Bulletpoint\Domain\Themes */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var int[] */
	private $tags;

	public function __construct(Themes $origin, array $tags, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
		$this->tags = $tags;
	}

	public function create(array $theme): int {
		return $this->origin->create($theme);
	}

	public function all(Dataset\Selection $selection): \Iterator {
		$themes = (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect([
					'id',
					'name',
					'alternative_names',
					'tags',
					'reference_url',
					'related_themes_id',
					'user_id',
					'created_at',
					'is_starred',
					'starred_at',
				]))->from(['web.tagged_themes'])
					->whereIn('tag_id', ['tag_id' => $this->tags]),
				$selection,
			),
		))->rows();
		foreach ($themes as $theme) {
			yield new StoredTheme(
				$theme['id'],
				new Storage\MemoryConnection($this->connection, $theme),
				new Access\FakeUser(),
			);
		}
	}

	public function count(Dataset\Selection $selection): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect(['count(*)']))
					->from(['web.tagged_themes'])
					->whereIn('tag_id', ['tag_id' => $this->tags]),
				$selection,
			),
		))->field();
	}
}
