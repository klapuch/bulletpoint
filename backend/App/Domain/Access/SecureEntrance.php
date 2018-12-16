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
		['email' => $plainEmail, 'password' => $plainPassword] = array_map('strval', $credentials);
		$user = (new Storage\TypedQuery(
			$this->connection,
			'SELECT * FROM users WHERE email = ?',
			[$plainEmail]
		))->row();
		if (!$this->exists($user))
			throw new \UnexpectedValueException(t('access.bad.email', $plainEmail));
		elseif (!$this->cipher->decrypted($plainPassword, $user['password']))
			throw new \UnexpectedValueException(t('access.bad.password'));
		if ($this->cipher->deprecated($user['password']))
			$this->rehash($plainPassword, $user['id']);
		return new ConstantUser((string) $user['id'], $user);
	}

	private function exists(array $row): bool {
		return (bool) $row;
	}

	private function rehash(string $password, int $id): void {
		(new Storage\TypedQuery(
			$this->connection,
			'UPDATE users SET password = ? WHERE id = ?',
			[$this->cipher->encryption($password), $id]
		))->execute();
	}

	public function exit(): User {
		return new Guest();
	}
}
