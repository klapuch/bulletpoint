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

	/** @var int */
	private $tag;

	public function __construct(Themes $origin, int $tag, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
		$this->tag = $tag;
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
					'user_id',
					'created_at',
				]))->from(['web.tagged_themes'])
					->where('tag_id = :tag_id', ['tag_id' => $this->tag]),
				$selection
			)
		))->rows();
		foreach ($themes as $theme) {
			yield new StoredTheme(
				$theme['id'],
				new Storage\MemoryConnection($this->connection, $theme),
				new Access\FakeUser()
			);
		}
	}

	public function count(Dataset\Selection $selection): int {
		return (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Sql\AnsiSelect(['count(*)']))
					->from(['web.tagged_themes'])
					->where('tag_id = :tag_id', ['tag_id' => $this->tag]),
				$selection
			)
		))->field();
	}
}
