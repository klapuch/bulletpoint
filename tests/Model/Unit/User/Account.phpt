<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\User;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class Account extends TestCase\Database {
	public function testEmail() {
		Assert::same(
			'facedown@email.cz',
			(new User\Account(
				new Fake\Identity(1),
				$this->preparedDatabase(),
				new Fake\Cipher
			))->email()
		);
	}

	public function testPasswordChanging() {
		(new User\Account(
			new Fake\Identity(1),
			$this->preparedDatabase(),
			new Fake\Cipher
		))->changePassword('newPassword');
		Assert::same(
			['password' => 'encrypted'],
			$this->connection()->fetch(
				'SELECT `password` FROM users WHERE ID = 1'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, role, email, username, `password`)
			VALUES (1, "user", "facedown@email.cz", "facedown", "123456")'
		);
		return $connection;
	}
}


(new Account())->run();
