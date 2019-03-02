<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Tags {
	public function all(): array;

	/**
	 * @throws \UnexpectedValueException
	 * @param string $name
	 */
	public function add(string $name): void;
}
