<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Translation;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlUsernameSlug extends TestCase\Database {
	public function testStringSlug() {
		Assert::same(
			'sl-ug',
			(string)new Translation\MySqlUsernameSlug('sl-ug', new Fake\Database)
		);
	}

	public function testOrigin() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (ID, username) VALUES (9, "sl-ug")'
		);
		Assert::same(
			9,
			(new Translation\MySqlUsernameSlug('sl-ug', $connection))->origin()
		);
	}

	public function testRenaming() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (ID, username) VALUES (9, "sl-ug")'
		);
		$newUsername = 'abc-abc3_abc';
		$slug = new Translation\MySqlUsernameSlug('sl-ug', $connection);
		Assert::equal(
			new Translation\MySqlUsernameSlug($newUsername, $connection),
			$slug->rename($newUsername)
		);
		Assert::same(
			$newUsername,
			$connection->fetchColumn('SELECT username FROM users WHERE ID = 9')
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Přezdívka "sl-ug2" již existuje
	*/
	public function testRenamingToExistingOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (ID, username) VALUES
			(9, "sl-ug"), (10, "sl-ug2")'
		);
		(new Translation\MySqlUsernameSlug('sl-ug', $connection))
		->rename('sl-ug2');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		return $connection;
	}
}


(new MySqlUsernameSlug())->run();
