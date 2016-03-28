<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Report;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlComplaint extends TestCase\Database {
	public function testId() {
		Assert::same(
			1,
			(new Report\MySqlComplaint(
				1,
				new Fake\Identity(1),
				new Fake\Database
			))->id()
		);
	}

	public function testCritic() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (user_id) VALUES (1)'
		);
		Assert::equal(
			new Access\MySqlIdentity(1, $connection),
			(new Report\MySqlComplaint(
				1,
				new Fake\Identity(1),
				$connection
			))->critic()
		);
	}

	public function testTarget() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (comment_id) VALUES (1)'
		);
		Assert::equal(
			new Report\Target(1),
			(new Report\MySqlComplaint(
				1,
				new Fake\Identity(1),
				$connection
			))->target()
		);
	}

	public function testReason() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (reason) VALUES ("jine")'
		);
		Assert::same(
			'Jiné',
			(new Report\MySqlComplaint(
				1,
				new Fake\Identity(1),
				$connection
			))->reason()
		);
	}

	public function testSettling() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (reason, settled) VALUES ("jine", 0)'
		);
		(new Report\MySqlComplaint(
			1,
			new Fake\Identity(1),
			$connection
		))->settle();
		Assert::same(
			1,
			$connection->fetchColumn(
				'SELECT settled FROM comment_complaints'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comment_complaints');
		return $connection;
	}
}


(new MySqlComplaint())->run();
