<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

/**
 * Entrance creating refresh tokens
 */
final class RefreshingEntrance implements Entrance {
	/**
	 * @param mixed[] $credentials
	 * @throws \UnexpectedValueException
	 */
	public function enter(array $credentials): User {
		session_write_close();
		session_id($credentials['token']);
		session_start();
		session_regenerate_id(false);
		if (!isset($_SESSION[self::IDENTIFIER])) {
			session_destroy();
			throw new \UnexpectedValueException('Provided token is not valid.');
		}
		return new SessionUser(new ConstantUser(session_id(), []));
	}

	public function exit(): User {
		return new Guest();
	}
}
