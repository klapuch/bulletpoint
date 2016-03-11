<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Core\{Storage, Security};

final class MySqlForgottenPassword implements ForgottenPassword {
	private $reminder;
	private $database;
	private $cipher;

	public function __construct(
		string $reminder,
		Storage\Database $database,
		Security\Cipher $cipher
	) {
		$this->reminder = $reminder;
		$this->database = $database;
		$this->cipher = $cipher;
	}

	public function change(string $password) {
		$this->database->query(
			'UPDATE users
			SET `password` = ?
			WHERE ID = (
				SELECT user_id
				FROM forgotten_passwords
				WHERE reminder = ?
			)',
			[$this->cipher->encrypt($password), $this->reminder]
		);
		$this->database->query(
			'UPDATE forgotten_passwords
			SET used = 1
			WHERE reminder = ?',
			[$this->reminder]
		);
	}
}