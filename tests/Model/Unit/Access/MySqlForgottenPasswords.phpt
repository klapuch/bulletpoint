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

final class MySqlForgottenPasswords extends TestCase\Database {
	public function testReminding() {
		$connection = $this->preparedDatabase();
        (new Access\MySqlForgottenPasswords(
            $connection,
            new Fake\Cipher
        ))->remind('foo@bar.cz');
		Assert::same(
			[
				'user_id' => 1,
				'reminder_length' => 141,
				'reminded_at' => date('j.n.Y H:i'),
				'used' => 0
			],
			$connection->fetch(
				'SELECT user_id,
				LENGTH(reminder) AS reminder_length,
				DATE_FORMAT(reminded_at, "%e.%c.%Y %H:%i") AS reminded_at,
				used
				FROM forgotten_passwords'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE forgotten_passwords');
		$connection->query('TRUNCATE users');
        $connection->query(
            'INSERT INTO users (`password`, email) VALUES ("123", "foo@bar.cz")'
        );
		return $connection;
	}
}


(new MySqlForgottenPasswords())->run();
