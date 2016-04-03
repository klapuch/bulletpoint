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
			new Fake\Bulletpoints()
		))->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpoint(
				$owner,
				'second',
				new \Datetime('1999-01-01 01:01:01'),
                new Wiki\MySqlInformationSource(2, $connection),
				new Wiki\MySqlBulletpoint(2, $connection),
                new Wiki\ConstantDocument(
                    'fooTitle',
                    'fooDescription',
                    new Access\MySqlIdentity(666, $connection),
                    new \DateTime('2000-01-01'),
                    new Wiki\MySqlInformationSource(
                        100,
                        $connection
                    ),
                    new Wiki\MySqlDocument(9, $connection)
                )
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query('TRUNCATE documents');
		$connection->query(
			'INSERT INTO bulletpoints
			(ID, content, user_id, information_source_id, document_id, created_at)
			VALUES
			(1, "first", 1, 1, 9, "2000-01-01 01:01:01"),
			(2, "second", 2, 2, 9, "1999-01-01 01:01:01")'
		);
        $connection->query(
            'INSERT INTO documents
            (ID, title, description, created_at, user_id, information_source_id)
            VALUES (9, "fooTitle", "fooDescription", "2000-01-01", 666, 100)'
        );
		return $connection;
	}
}


(new OwnedMySqlBulletpoints())->run();
