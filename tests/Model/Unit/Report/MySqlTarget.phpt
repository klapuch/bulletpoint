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

final class MySqlTarget extends TestCase\Database {
	public function testComplaints() {
		$connection = $this->preparedDatabase();
		$myself = new Fake\Identity(1);
		$complaints = (new Report\MySqlTarget(1, $myself, $connection))
		->complaints();
		Assert::equal(
			new Report\ConstantComplaint(
				new Access\ConstantIdentity(
					1,
					new Access\ConstantRole(
						'user',
						new Access\MySqlRole(1, $connection)
					),
					'face'
				),
				new Report\MySqlTarget(1, $myself, $connection),
				'Vulgarita',
				new Report\MySqlComplaint(1, $myself, $connection)
			),
			$complaints->current()
		);
		$complaints->next();
		Assert::false($complaints->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comment_complaints');
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO comment_complaints
			(ID, comment_id, settled, user_id, reason)
			VALUES (1, 1, 0, 1, "vulgarita")'
		);
		$connection->query(
			'INSERT INTO users (ID, role, username) VALUES (1, "user", "face")'
		);
		return $connection;
	}
}


(new MySqlTarget())->run();
