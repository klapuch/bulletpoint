<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Access;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlForgottenPassword extends TestCase\Database {
	public function testChanging() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (`password`, email) VALUES ("123", "foo@bar.cz")'
		);
		$connection->query(
			'INSERT INTO forgotten_passwords
			(user_id, used, reminder)
			VALUES (1, 0, "123456")'
		);
		(new Access\MySqlForgottenPassword(
			'123456',
			$connection,
			new Fake\Cipher
		))->change('123456789');
		Assert::same(
			'encrypted',
			$connection->fetchColumn(
				'SELECT `password` FROM users WHERE ID = 1'
			)
		);
		Assert::same(
			1,
			$connection->fetchColumn(
				'SELECT used
				FROM forgotten_passwords
				WHERE user_id = 1'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE forgotten_passwords');
		$connection->query('TRUNCATE users');
		return $connection;
	}
}


(new MySqlForgottenPassword())->run();
