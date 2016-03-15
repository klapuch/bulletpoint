<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Core\{Storage, Security};
use Bulletpoint\Model\User;
use Bulletpoint\Exception;

final class TemporaryLogin implements Login {
	private $database;
	private $cipher;

	public function __construct(
		Storage\Database $database,
		Security\Cipher $cipher
	) {
		$this->database = $database;
		$this->cipher = $cipher;
	}

	public function enter(User\User $user): Identity {
		list($id, $password, $role) = $this->database->fetch(
			'SELECT ID, `password`, role FROM users WHERE username = ?',
			[$user->username()],
			\PDO::FETCH_NUM
		);
		if(!$this->exists($id))
			throw new Exception\AccessDeniedException('Uživatel neexistuje');
		elseif(!$this->activated($id))
			throw new Exception\AccessDeniedException('Účet není aktivován');
		elseif(!$this->cipher->decrypt($user->password(), $password))
			throw new Exception\AccessDeniedException('Nesprávné heslo');
		return new ConstantIdentity(
			$id,
			new ConstantRole($role, new MySqlRole($id, $this->database)),
			$user->username()
		);
	}

	private function exists($id): bool {
		return (int)$id !== 0;
	}

	private function activated(int $id): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM verification_codes WHERE user_id = ? AND used = 1',
			[$id]
		);
	}
}