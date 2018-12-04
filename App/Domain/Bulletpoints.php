<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Bulletpoints {
	public function count(): int;

	public function add(array $bulletpoint): void;

	/**
	 * @return \Iterator|\Bulletpoint\Domain\Bulletpoint[]
	 */
	public function all(): \Iterator;
}
