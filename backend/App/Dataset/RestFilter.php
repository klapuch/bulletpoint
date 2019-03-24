<?php
declare(strict_types = 1);

namespace Bulletpoint\Dataset;

use Klapuch\Dataset;

/**
 * Rest filter without our taken parameters
 */
final class RestFilter extends Dataset\Filter {
	/** @var mixed[] */
	private $criteria;

	/** @var string[] */
	private $allowedCriteria;

	/**
	 * @param mixed[] $criteria
	 * @param string[] $allowedCriteria
	 */
	public function __construct(array $criteria, array $allowedCriteria = []) {
		$this->criteria = $criteria;
		$this->allowedCriteria = $allowedCriteria;
	}

	protected function filter(): array {
		return (new Dataset\RestFilter(
			array_intersect_key($this->criteria, array_flip($this->allowedCriteria)),
			['page', 'per_page', 'sort', 'fields'],
		))->filter();
	}
}
