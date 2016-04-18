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

final class MySqlPunishment extends TestCase\Database {
	public function testExpiredPunishment() {
		Assert::true(
			(new Constraint\MySqlPunishment(2, $this->preparedDatabase()))->expired()
		);
	}

	public function testForgivenOngoingPunishment() {
		Assert::true(
			(new Constraint\MySqlPunishment(1, $this->preparedDatabase()))->expired()
		);
	}

	public function testOngoingPunishment() {
		Assert::false(
			(new Constraint\MySqlPunishment(3, $this->preparedDatabase()))->expired()
		);
	}

	public function testReason() {
		Assert::same(
			'rude',
			(new Constraint\MySqlPunishment(1, $this->preparedDatabase()))->reason()
		);
	}

	public function testId() {
		Assert::same(1, (new Constraint\MySqlPunishment(1, new Fake\Database))->id());
	}

	public function testExpiration() {
		Assert::equal(
			new \DateTimeImmutable('2100-01-01 01:01:01'),
			(new Constraint\MySqlPunishment(1, $this->preparedDatabase()))->expiration()
		);
	}

	public function testSinner() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query('INSERT INTO users (ID, role) VALUES (2, "user")');
		Assert::equal(
			new Access\MySqlIdentity(2, $connection),
			(new Constraint\MySqlPunishment(1, $connection))->sinner()
		);
	}

	public function testForgiving() {
		$connection = $this->preparedDatabase();
		(new Constraint\MySqlPunishment(3, $connection))->forgive();
		Assert::same(
			1,
			$connection->fetchColumn('SELECT forgiven FROM punishments WHERE ID = 3')
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE punishments');
		$connection->query(
			'INSERT INTO punishments
			(sinner_id, author_id, reason, expiration, forgiven)
			VALUES
			(2, 1, "rude", "2100-01-01 01:01:01", 1),
			(3, 1, "rude", "2000-01-01 01:01:01", 0),
			(4, 1, "rude", NOW() + INTERVAL 1 DAY, 0)'
		);
		return $connection;
	}
}


(new MySqlPunishment())->run();
