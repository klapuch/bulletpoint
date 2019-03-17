<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

interface User {
	/**
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	public function id(): string;

	/**
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	public function properties(): array;

	/**
	 * @throws \UnexpectedValueException
	 * @param mixed[] $properties
	 */
	public function edit(array $properties): void;
}
