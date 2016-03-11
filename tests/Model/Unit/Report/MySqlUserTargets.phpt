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

final class MySqlUserTargets extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$myself = new Fake\Identity(3);
		$rows = (new Report\MySqlUserTargets($myself, $connection))
		->iterate();
		Assert::equal(
			new Report\ConstantTarget(
				5,
				new \ArrayIterator(
					[new Report\MySqlComplaint(2, $myself, $connection)]
				)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comment_complaints');
		$connection->query(
			'INSERT INTO comment_complaints (ID, comment_id, settled, user_id)
			VALUES (2, 5, 0, 3), (3, 6, 1, 3)'
		);
		return $connection;
	}
}


(new MySqlUserTargets())->run();
