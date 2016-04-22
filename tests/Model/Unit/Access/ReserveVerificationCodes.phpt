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

final class ReserveVerificationCodes extends TestCase\Database {
	public function testRegenerating() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, code, used)
			VALUES (6, "123456", 0)'
		);
		$code = (new Access\ReserveVerificationCodes($connection))
		->generate('foo@bar.cz');
		Assert::equal(
			new Access\MySqlVerificationCode('123456', $connection),
			$code
		);
	}

	/**
	* @throws \Bulletpoint\Exception\ExistenceException Ověřovací kód již byl použit
	*/
	public function testRegeneratingForUsedOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, code, used)
			VALUES (6, "123456", 1)'
		);
		(new Access\ReserveVerificationCodes($connection))
		->generate('foo@bar.cz');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE verification_codes');
		$connection->query(
			'INSERT INTO users (ID, email) VALUES (6, "foo@bar.cz")'
		);
		return $connection;
	}
}


(new ReserveVerificationCodes())->run();
