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

final class MySqlComplaints extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (comment_id, settled, reason)
			VALUES (1, 0, "Jiné"), (1, 0, "Jiné"), (1, 0, "Spam"),
			(2, 0, "Vulgarita")'
		);
		$rows = (new Report\MySqlComplaints(
			new Fake\Identity(1),
			$connection
		))->iterate();
		Assert::same(['reason' => 'Jiné', 'target' => 1], $rows->current());
		Assert::same(3, $rows->key());
		$rows->next();
		Assert::same(['reason' => 'Spam', 'target' => 1], $rows->current());
		Assert::same(1, $rows->key());
		$rows->next();
		Assert::same(['reason' => 'Vulgarita', 'target' => 2], $rows->current());
		Assert::same(1, $rows->key());
		$rows->next();
		Assert::false($rows->valid());
	}

	public function testSettling() {
		$connection = $this->preparedDatabase();
		(new Report\MySqlComplaints(
			new Fake\Identity(1),
			$connection
		))->settle(new Fake\Target(1));
		Assert::same(
			1,
			$connection->fetchColumn(
				'SELECT settled FROM comment_complaints'
			)
		);
	}

	public function testComplaining() {
		$connection = $this->preparedDatabase();
		(new Report\MySqlComplaints(
			new Fake\Identity(2),
			$connection
		))->complain(new Fake\Target(1), 'vulgarita');
		Assert::same(
			[
				'comment_id' => 1,
				'user_id' => 2,
				'reason' => 'Vulgarita'
			],
			$connection->fetch(
				'SELECT comment_id, user_id, reason
				FROM comment_complaints WHERE user_id = 2'
			)
		);
	}

	/**
	* @throws OverflowException Tento komentář jsi již nahlásil
	*/
	public function testAlreadyCompalinedComment() {
		$connection = $this->preparedDatabase();
		(new Report\MySqlComplaints(new Fake\Identity(1), $connection))
		->complain(new Fake\Target(1), 'rude');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comment_complaints');
		$connection->query(
			'INSERT INTO comment_complaints
			(ID, comment_id, settled, reason, user_id)
			VALUES (1, 1, 0, "Jiné", 1)'
		);
		return $connection;
	}
}


(new MySqlComplaints())->run();
