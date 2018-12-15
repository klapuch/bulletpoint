<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

interface Entrance {
	public const IDENTIFIER = 'id';
	/**
	 * Let the user in
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User;

	/**
	 * Let the user out
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function exit(): User;
}
