<?php
declare(strict_types = 1);

namespace Bulletpoint\Schema;

interface Enum {
	/**
	 * Available values in enum
	 * @return array
	 */
	public function values(): array;
}
