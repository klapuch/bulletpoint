<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Dataset;
use Klapuch\Sql;
use Klapuch\Storage;

final class SearchedThemes implements Themes {
	/** @var \Bulletpoint\Domain\Themes */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var string */
	private $keyword;

	public function __construct(Themes $origin, string $keyword, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
		$this->keyword = $keyword;
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
					'tags',
					'reference_url',
					'user_id',
					'created_at',
				]))->from(['web.themes'])
					->where('name ILIKE :keyword', ['keyword' => sprintf('%%%s%%', $this->keyword)]),
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
					->from(['web.themes'])
					->where('name ILIKE :keyword', ['keyword' => sprintf('%%%s%%', $this->keyword)]),
				$selection
			)
		))->field();
	}
}
