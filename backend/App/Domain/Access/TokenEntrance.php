<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

/**
 * Entrance creating tokens
 */
final class TokenEntrance implements Entrance {
	/** @var \Bulletpoint\Domain\Access\Entrance */
	private $origin;

	public function __construct(Entrance $origin) {
		$this->origin = $origin;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		if (session_status() === PHP_SESSION_NONE)
			session_start();
		session_regenerate_id(true);
		$_SESSION[self::IDENTIFIER] = (int) $user->id();
		return new SessionUser($user);
	}

	/**
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function exit(): User {
		if (session_status() === PHP_SESSION_ACTIVE)
			unset($_SESSION[self::IDENTIFIER]);
		return $this->origin->exit();
	}
}
