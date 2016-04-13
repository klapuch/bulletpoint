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

final class LimitedForgottenPasswords extends TestCase\Database {
	/**
	* @throws \OverflowException Byl překročen limit 3 zapomenutých hesel během 24 hodin
	*/
	public function testLimitedReminding() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (user_id, reminded_at, reminder)
			VALUES 
			(1, NOW() - INTERVAL 1 HOUR, "reminder1"),
			(1, NOW() - INTERVAL 2 HOUR, "reminder2"),
			(1, NOW() - INTERVAL 3 HOUR, "reminder3")'
		);
		(new Access\LimitedForgottenPasswords(
			new Fake\ForgottenPasswords,
			$connection
		))->remind('foo@gmail.com');
	}

	public function testValidReminding() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (user_id, reminded_at, reminder)
			VALUES 
			(1, NOW(), "reminder0"),
			(1, NOW() - INTERVAL 25 HOUR, "reminder1"),
			(1, NOW() - INTERVAL 25 HOUR, "reminder2"),
			(1, NOW() - INTERVAL 25 HOUR, "reminder3"),
			(1, NOW() - INTERVAL 24 HOUR, "reminder4"),
			(1, NOW() - INTERVAL 24 HOUR, "reminder5"),
			(1, NOW() - INTERVAL 24 HOUR, "reminder6"),
			(1, NOW() - INTERVAL 26 HOUR, "reminder7")'
		);
		(new Access\LimitedForgottenPasswords(
			new Fake\ForgottenPasswords,
			$connection
		))->remind('foo@gmail.com');
		Assert::true(true);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE forgotten_passwords');
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, email) VALUES (1, "foo@gmail.com")'
		);
		return $connection;
	}
}


(new LimitedForgottenPasswords())->run();
