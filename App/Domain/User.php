<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface User {
	/**
	 * @throws \UnexpectedValueException
	 * @return int
	 */
	public function id(): int;

	/**
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	public function properties(): array;
}
