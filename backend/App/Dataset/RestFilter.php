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
	private $allows;

	/** @var string[] */
	private $ignores;

	/**
	 * @param mixed[] $criteria
	 * @param string[] $allows
	 * @param string[] $ignores
	 */
	public function __construct(array $criteria, array $allows, array $ignores = []) {
		$this->criteria = $criteria;
		$this->allows = $allows;
		$this->ignores = $ignores;
	}

	protected function filter(): array {
		return (new Dataset\RestFilter(
			array_intersect_key($this->criteria, array_flip($this->allows)),
			array_merge(['page', 'per_page', 'sort', 'fields'], $this->ignores),
		))->filter();
	}
}
