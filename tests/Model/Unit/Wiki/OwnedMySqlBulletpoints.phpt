<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Wiki;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class OwnedMySqlBulletpoints extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
        $owner = new Fake\Identity(2);
		$rows = (new Wiki\OwnedMySqlBulletpoints(
			$owner,
			$connection,
			new Fake\Bulletpoints(new Fake\Database)
		))->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpoint(
				$owner,
				'second',
				new \Datetime('1999-01-01 01:01:01'),
                new Wiki\MySqlInformationSource(2, $connection),
				new Wiki\MySqlBulletpoint(2, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO bulletpoints
			(ID, content, user_id, information_source_id, document_id, created_at)
			VALUES
			(1, "first", 1, 1, 1, "2000-01-01 01:01:01"),
			(2, "second", 2, 2, 1, "1999-01-01 01:01:01")'
		);
		return $connection;
	}
}


(new OwnedMySqlBulletpoints())->run();
