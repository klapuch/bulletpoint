<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Sql;

use Bulletpoint\Sql\DecoratedWhere;
use Characterice\Sql\Clause;
use Characterice\Sql\Statement\Insert;
use Characterice\Sql\Statement\Update;
use Characterice\Sql\Statement\Delete;
use Characterice\Sql\Statement\Select;
use Characterice\Sql\Expression;

final class SearchedThemes {
	/**
	 * @var string
	 */
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
