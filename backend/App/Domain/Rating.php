<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Rating {
	/**
	 * @throws \UnexpectedValueException
	 * @param int $points
	 */
	public function rate(int $points): void;
}
