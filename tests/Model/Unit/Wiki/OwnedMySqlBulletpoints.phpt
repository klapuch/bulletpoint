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
		$rows = (new Wiki\OwnedMySqlBulletpoints(
			new Fake\Identity(2),
			$connection,
			new Fake\Bulletpoints(new Fake\Database)
		))->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpoint(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'administrator',
						new Access\MySqlRole(2, $connection)
					),
					'facedown'
				),
				'second',
				new \Datetime('1999-01-01 01:01:01'),
				new Wiki\ConstantInformationSource(
					'book',
					1998,
					'čapek',
					new Wiki\MySqlInformationSource(2, $connection)
				),
				new Wiki\MySqlBulletpoint(2, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE information_sources');
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO users (ID, role, username, email) VALUES
            (1, "member", "cucak", "e"), (2, "administrator", "facedown", "e2")'
		);
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, author, `year`)
			VALUES
			(1, "wikipedie", "facedown", 2005),
			(2, "book", "čapek", 1998)'
		);
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
