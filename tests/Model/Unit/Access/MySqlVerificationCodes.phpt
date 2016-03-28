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

final class MySqlVerificationCodes extends TestCase\Database {
	public function testGenerating() {
		$connection = $this->preparedDatabase();
		(new Access\MySqlVerificationCodes($connection))
		->generate('foo@bar.cz');
		Assert::same(
			91,
			$connection->fetchColumn(
				'SELECT LENGTH(code) FROM verification_codes WHERE user_id = 6'
			)
		);
	}
	
	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE verification_codes');
        $connection->query('TRUNCATE users');
        $connection->query(
            'INSERT INTO users (ID, email) VALUES (6, "foo@bar.cz")'
        );
		return $connection;
	}
}


(new MySqlVerificationCodes())->run();
