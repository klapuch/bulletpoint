<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

/**
 * Entrance to API with valid token
 */
final class ApiEntrance implements Entrance {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function enter(array $headers): User {
		if ($this->authorized(array_change_key_case($headers, CASE_LOWER)))
			return new RegisteredUser((int) $_SESSION[self::IDENTIFIER], $this->connection);
		return new Guest();
	}

	private function authorized(array $headers): bool {
		if (preg_match('~^[\w\-,]{22,256}$~', $this->token($headers)) === 1) {
			session_id($this->token($headers));
			if (session_status() === PHP_SESSION_NONE)
				session_start();
			return isset($_SESSION[self::IDENTIFIER]);
		}
		return false;
	}

	private function token(array $headers): string {
		return explode(' ', $headers['authorization'] ?? '', 2)[1] ?? '';
	}

	public function exit(): User {
		return new Guest();
	}
}
