<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\{Constraint, Access};
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlBan extends TestCase\Database {
	public function testExpiredBan() {
		Assert::true(
			(new Constraint\MySqlBan(2, $this->preparedDatabase()))->expired()
		);
	}

	public function testOngoingBan() {
		Assert::false(
			(new Constraint\MySqlBan(1, $this->preparedDatabase()))->expired()
		);
	}

	public function testReason() {
		Assert::same(
			'rude',
			(new Constraint\MySqlBan(1, $this->preparedDatabase()))->reason()
		);
	}

	public function testId() {
		Assert::same(1, (new Constraint\MySqlBan(1, new Fake\Database))->id());
	}

	public function testExpiration() {
		Assert::equal(
			new \Datetime("2100-01-01 01:01:01"),
			(new Constraint\MySqlBan(1, $this->preparedDatabase()))->expiration()
		);
	}

	public function testIfBanIsExpired() {
		$connection = $this->preparedDatabase();
		Assert::false(
			(new Constraint\MySqlBan(1, $connection))->expired()
		);
		Assert::true(
			(new Constraint\MySqlBan(2, $connection))->expired()
		);
	}

	public function testSinner() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query('INSERT INTO users (ID, role) VALUES (2, "user")');
		Assert::equal(
			new Access\MySqlIdentity(2, $connection),
			(new Constraint\MySqlBan(1, $connection))->sinner()
		);
	}

	public function testCanceling() {
		$connection = $this->preparedDatabase();
		(new Constraint\MySqlBan(3, $connection))->cancel();
		Assert::same(
			1,
			$connection->fetchColumn('SELECT canceled FROM banned_users WHERE ID = 3')
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE banned_users');
		$connection->query(
			'INSERT INTO banned_users
			(ID, user_id, author_id, reason, expiration, canceled)
			VALUES
			(1, 2, 1, "rude", "2100-01-01 01:01:01", 1),
			(2, 3, 1, "rude", "2000-01-01 01:01:01", 0),
			(3, 4, 1, "rude", NOW() + INTERVAL 1 DAY, 0)'
		);
		return $connection;
	}
}


(new MySqlBan())->run();
