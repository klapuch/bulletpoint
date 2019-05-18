<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Sql;

use Characterice\Sql\Clause;
use Characterice\Sql\Expression;
use Characterice\Sql\Statement\Select;

final class SearchedThemes {
	/** @var string */
	private $keyword;

	public function __construct(string $keyword) {
		$this->keyword = $keyword;
	}

	public function query(): Select\Query {
		return (new Select\Query())
			->from(new Expression\From(['themes' => 'web.tagged_themes']))
			->join(new Clause\LeftJoin('theme_alternative_names', 'theme_alternative_names.theme_id = themes.id'))
			->where(new Expression\RawWhere('themes.name ILIKE :keyword', ['keyword' => sprintf('%%%s%%', $this->keyword)]))
			->orWhere(new Expression\RawWhere('theme_alternative_names.name ILIKE :keyword'));
	}
}
