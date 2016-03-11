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

final class MySqlVerificationCode extends TestCase\Database {
	public function testSuccessfulActivation() {
		$connection = $this->preparedValidCode();
		$code = new Access\MySqlVerificationCode(
			'valid:code',
			$connection
		);
		$code->use();
		Assert::same(
			1,
			$connection->fetchColumn(
				'SELECT used FROM verification_codes WHERE code = "valid:code"'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Ověřovací kód již byl použit
	*/
	public function testAlreadActivatedCode() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, code, used)
			VALUES (2, "activated:code", 1)'
		);
		(new Access\MySqlVerificationCode(
			'activated:code',
			$connection
		))->use();
	}

	public function testOwner() {
		$this->preparedValidCode();
		$identity = (new Access\MySqlVerificationCode(
			'valid:code',
			$this->connection()
		))->owner();
		Assert::same(1, $identity->id());
		Assert::same('user', (string)$identity->role());
		Assert::same('face', $identity->username());
	}

	private function preparedValidCode() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO verification_codes (user_id, code, used)
			VALUES (1, "valid:code", 0)'
		);
		$connection->query(
			'INSERT INTO users (ID, role, username) VALUES (1, "user", "face")'
		);
		return $connection;
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE verification_codes');
		return $connection;
	}
}


(new MySqlVerificationCode())->run();
