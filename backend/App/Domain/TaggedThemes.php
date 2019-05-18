<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;
use Characterice\Sql\Clause;
use Characterice\Sql\Statement\Insert;
use Characterice\Sql\Statement\Update;
use Characterice\Sql\Statement\Delete;
use Characterice\Sql\Statement\Select;
use Characterice\Sql\Expression;
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
				(new Select\Query())
					->select(new Expression\Select([
						'id',
						'name',
						'alternative_names',
						'tags',
						'reference_url',
						'reference_is_broken',
						'related_themes_id',
						'user_id',
						'created_at',
						'is_starred',
						'starred_at',
						'is_empty',
					]))->from(new Expression\From(['web.tagged_themes']))
						->where(new Expression\WhereIn('tag_id', $this->tags)),
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
				(new Select\Query())
					->select(new Expression\Select(['count(*)']))
					->from(new Expression\From(['web.tagged_themes']))
					->where(new Expression\WhereIn('tag_id', $this->tags)),
				$selection,
			),
		))->field();
	}
}
