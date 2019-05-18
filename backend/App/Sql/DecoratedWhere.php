<?php
declare(strict_types = 1);

namespace Bulletpoint\Sql;

use Characterice\Sql\Clause;
use Characterice\Sql\Statement\Insert;
use Characterice\Sql\Statement\Update;
use Characterice\Sql\Statement\Delete;
use Characterice\Sql\Statement\Select;
use Characterice\Sql\Expression;

abstract class DecoratedWhere implements Sql\Where {
	/** @var \Klapuch\Sql\Where */
	private $origin;

	public function __construct(Sql\Where $origin) {
		$this->origin = $origin;
	}

	public function whereIn(string $column, array $parameters = []): Sql\Where {
		return $this->origin->whereIn($column, $parameters);
	}

	public function where(string $condition, array $parameters = []): Sql\Where {
		return $this->origin->where($condition, $parameters);
	}

	public function orWhere(string $condition, array $parameters = []): Sql\Where {
		return $this->origin->orWhere($condition, $parameters);
	}

	public function groupBy(array $columns): Sql\GroupBy {
		return $this->origin->groupBy($columns);
	}

	public function having(string $condition, array $parameters = []): Sql\Having {
		return $this->origin->having($condition, $parameters);
	}

	public function orderBy(array $orders): Sql\OrderBy {
		return $this->origin->orderBy($orders);
	}

	public function returning(array $columns, array $parameters = []): Sql\Returning {
		return $this->origin->returning($columns, $parameters);
	}

	public function limit(int $limit): Sql\Limit {
		return $this->origin->limit($limit);
	}

	public function offset(int $offset): Sql\Offset {
		return $this->origin->offset($offset);
	}

	public function sql(): string {
		return $this->origin->sql();
	}

	public function parameters(): Sql\Parameters {
		return $this->origin->parameters();
	}
}
