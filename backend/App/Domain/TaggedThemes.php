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
					'pt.id',
					'pt.name',
					'pt.tags',
					'pt.reference_url',
					'pt.user_id',
					'pt.created_at',
				]))->from(['public_themes AS pt'])
					->join('INNER', 'theme_tags', 'theme_tags.theme_id = pt.id')
					->where('theme_tags.tag_id = :tag_id', ['tag_id' => $this->tag]),
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
				(new Sql\AnsiSelect(['count(*)']))
					->from(['public_themes AS pt'])
					->join('INNER', 'theme_tags', 'theme_tags.theme_id = pt.id')
					->where('theme_tags.tag_id = :tag_id', ['tag_id' => $this->tag]),
				$selection
			)
		))->field();
	}
}
