<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema;

interface Enum {
	/**
	 * Available values in enum
	 * @return array
	 */
	public function values(): array;

	/**
	 * Add new enum value
	 * @param string $name
	 * @return void
	 */
	public function add(string $name): void;
}
