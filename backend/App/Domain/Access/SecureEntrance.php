<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Encryption;
use Klapuch\Storage;

/**
 * Secure entrance for entering users to the system
 */
final class SecureEntrance implements Entrance {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Encryption\Cipher */
	private $cipher;

	public function __construct(Storage\Connection $connection, Encryption\Cipher $cipher) {
		$this->connection = $connection;
		$this->cipher = $cipher;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		['login' => $login, 'password' => $plainPassword] = array_map('strval', $credentials);
		$user = (new Storage\TypedQuery(
			$this->connection,
			'SELECT * FROM users WHERE email = :login OR username = :login',
			[$login],
		))->row();
		if (!self::exists($user)) {
			if (self::isEmail($login)) {
				throw new \UnexpectedValueException(t('access.bad.email', $login));
			}
			throw new \UnexpectedValueException(t('access.bad.username', $login));
		} elseif (!$this->cipher->decrypted($plainPassword, $user['password']))
			throw new \UnexpectedValueException(t('access.bad.password'));
		if ($this->cipher->deprecated($user['password']))
			$this->rehash($plainPassword, $user['id']);
		return new ConstantUser((string) $user['id'], $user);
	}

	private static function isEmail(string $login): bool {
		return strpos($login, '@') !== false;
	}

	private static function exists(array $row): bool {
		return (bool) $row;
	}

	private function rehash(string $password, int $id): void {
		(new Storage\TypedQuery(
			$this->connection,
			'UPDATE users SET password = ? WHERE id = ?',
			[$this->cipher->encryption($password), $id],
		))->execute();
	}

	public function exit(): User {
		return new Guest();
	}
}
