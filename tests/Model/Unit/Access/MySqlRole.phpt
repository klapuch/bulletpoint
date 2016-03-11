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

final class MySqlRole extends TestCase\Database {
	public function testRoleAsText() {
		Assert::same(
			'member',
			(string)new Access\MySqlRole(
				2,
				new Fake\Database($fetch = null, $fetchColumn = 'member')
			)
		);
	}

	public function testUndefinedRoleWithRank() {
		Assert::same(
			-1,
			(new Access\MySqlRole(
				2,
				new Fake\Database($fetch = null, $fetchColumn = 'foo')
			))->rank()
		);
	}

	public function testEmptyRole() {
		Assert::same(
			'guest',
			(string)new Access\MySqlRole(
				2,
				new Fake\Database($fetch = null, $fetchColumn = '')
			)
		);
		Assert::same(
			'guest',
			(string)new Access\MySqlRole(
				2,
				new Fake\Database($fetch = null, $fetchColumn = null)
			)
		);
	}	

	public function testPromoting() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (ID, role) VALUES (2, "member")'
		);
		$newRole = (new Access\MySqlRole(2, $connection))->promote();
		Assert::same(
			'administrator',
			(string)$newRole
		);
	}

	/**
	* @throws \OverflowException Vyšší role neexistuje
	*/
	public function testOverflowPromoting() {
		(new Access\MySqlRole(
			2,
			new Fake\Database($fetch = null, $fetchColumn = 'creator')
		))->promote();
	}

	public function testDegrading() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO users (ID, role) VALUES (2, "administrator")'
		);
		$newRole = (new Access\MySqlRole(2, $connection))->degrade();
		Assert::same(
			'member',
			(string)$newRole
		);
	}

	/**
	* @throws \UnderflowException Nižší role neexistuje
	*/
	public function testUnderflowDegrading() {
		(new Access\MySqlRole(
			2,
			new Fake\Database($fetch = null, $fetchColumn = 'member')
		))->degrade();
	}

	public function testRanks() {
		Assert::same((new Access\MySqlRole(
			1,
			new Fake\Database($fetch = null, $fetchColumn = 'member')
		))->rank(), 1);
		Assert::same((new Access\MySqlRole(
			1,
			new Fake\Database($fetch = null, $fetchColumn = 'administrator')
		))->rank(), 2);
		Assert::same((new Access\MySqlRole(
			1,
			new Fake\Database($fetch = null, $fetchColumn = 'creator')
		))->rank(), 3);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		return $connection;
	}
}


(new MySqlRole())->run();
