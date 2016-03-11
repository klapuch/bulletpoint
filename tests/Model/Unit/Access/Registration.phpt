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

final class Registration extends TestCase\Database {
	public function testSuccessfulRegistration() {
		$connection = $this->preparedDatabase();
		$identity = (new Access\Registration(
			$connection,
			new Fake\Cipher
		))->register(
			new User\Applicant(
				new User\User('facedown', '123456'),
				'facedown@email.cz'
			)
		);
		Assert::same(
			[
				'username' => 'facedown',
				'password' => 'encrypted',
				'email' => 'facedown@email.cz'
			],
			$connection->fetch(
				'SELECT username, `password`, email FROM users WHERE ID = 1'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		return $connection;
	}
}


(new Registration())->run();
