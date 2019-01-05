<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Sql;

use Bulletpoint\Sql\DecoratedWhere;
use Klapuch\Sql;

final class SearchedThemes extends DecoratedWhere {
	public function __construct(Sql\Select $select, string $keyword) {
		parent::__construct(
			$select->from(['web.themes'])
				->join('LEFT', 'theme_alternative_names', 'theme_alternative_names.theme_id = themes.id')
				->where('themes.name ILIKE :keyword OR theme_alternative_names.name ILIKE :keyword', ['keyword' => sprintf('%%%s%%', $keyword)]),
		);
	}
}
