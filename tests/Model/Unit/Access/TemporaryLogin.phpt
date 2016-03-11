<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\{Access, User};
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class TemporaryLogin extends TestCase\Database {
	public function testSuccessfulLogin() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 1)'
		);
		$identity = (new Access\TemporaryLogin(
			$connection,
			new Fake\Cipher
		))->enter(new User\User('facedown', '123456'));
		Assert::same($identity->id(), 1);
		Assert::same((string)$identity->role(), 'user');
		Assert::same($identity->username(), 'facedown');
	}

	/**
	* @throws Bulletpoint\Exception\AccessDeniedException Účet není aktivován
	*/
	public function testLoginWithoutAccountActivation() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 0)'
		);
		$identity = (new Access\TemporaryLogin(
			$connection,
			new Fake\Cipher
		))->enter(new User\User('facedown', '123456'));
		Assert::same($identity->id(), 1);
		Assert::same((string)$identity->role(), 'user');
		Assert::same($identity->username(), 'facedown');
	}

	/**
	* @throws Bulletpoint\Exception\AccessDeniedException Uživatel neexistuje
	*/
	public function testUnknownUser() {
		$connection = $this->preparedDatabase();
		$identity = (new Access\TemporaryLogin(
			$connection,
			new Fake\Cipher
		))->enter(new User\User('foooooo', '123456'));
	}

	/**
	* @throws Bulletpoint\Exception\AccessDeniedException Nesprávné heslo
	*/
	public function testWrongPassword() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 1)'
		);
		(new Access\TemporaryLogin(
			$connection,
			new Fake\Cipher($decrypted = false)
		))->enter(new User\User('facedown', '123456789'));
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE verification_codes');
		$connection->query(
			'INSERT INTO users (ID, username, password, email, role) 
			VALUES (1, "facedown", "123456", "facedown@email.cz", "user")'
		);
		return $connection;
	}
}


(new TemporaryLogin())->run();
