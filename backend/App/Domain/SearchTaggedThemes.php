<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Bulletpoint\Domain;
use Characterice\Sql\Expression;
use Klapuch\Dataset;
use Klapuch\Storage;

final class SearchTaggedThemes implements Themes {
	/** @var \Bulletpoint\Domain\Themes */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var string */
	private $keyword;

	/** @var int[] */
	private $tags;

	public function __construct(Themes $origin, string $keyword, array $tags, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
		$this->keyword = $keyword;
		$this->tags = $tags;
	}

	public function create(array $theme): int {
		return $this->origin->create($theme);
	}

	public function all(Dataset\Selection $selection): \Iterator {
		$themes = (new Storage\BuiltQuery(
			$this->connection,
			new Dataset\SelectiveStatement(
				(new Domain\Sql\SearchedThemes($this->keyword))
					->query()
					->select(new Expression\Select([
						'DISTINCT ON (themes.id) themes.id',
						'themes.name',
						'themes.alternative_names',
						'themes.tags',
						'themes.reference_url',
						'themes.reference_is_broken',
						'themes.user_id',
						'themes.created_at',
						'themes.is_starred',
						'themes.starred_at',
						'themes.is_empty',
						'themes.related_themes_id',
					]))->where(new Expression\WhereIn('tag_id', $this->tags)),
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
				(new Domain\Sql\SearchedThemes($this->keyword))
					->query()
					->select(new Expression\Select(['count(DISTINCT themes.id)']))
					->where(new Expression\WhereIn('tag_id', $this->tags)),
				$selection,
			),
		))->field();
	}
}
