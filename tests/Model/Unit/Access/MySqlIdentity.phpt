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

final class MySqlIdentity extends TestCase\Database {
	public function testId() {
		Assert::same(
			100,
			(new Access\MySqlIdentity(100, new Fake\Database))->id()
		);
	}

	public function testRole() {
		$connection = $this->preparedDatabase();
		Assert::equal(
			(new Access\MySqlIdentity(1, $connection))->role(),
			new Access\MySqlRole(
				1,
				$connection
			)
		);
	}

	public function testUsername() {
		Assert::same(
			'facedown',
			(new Access\MySqlIdentity(1, $this->preparedDatabase()))->username()
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, role, username)
			VALUES (1, "admin", "facedown")'
		);
		return $connection;
	}
}


(new MySqlIdentity())->run();
