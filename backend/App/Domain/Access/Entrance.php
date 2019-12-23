<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

interface Entrance {
	public const IDENTIFIER = 'id';
	/**
	 * Let the user in
	 * @param mixed[] $credentials
	 * @throws \UnexpectedValueException
	 */
	public function enter(array $credentials): User;

	/**
	 * Let the user out
	 * @throws \UnexpectedValueException
	 */
	public function exit(): User;
}
