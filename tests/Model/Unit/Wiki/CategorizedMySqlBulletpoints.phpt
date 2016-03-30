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

final class CategorizedMySqlBulletpoints extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$rows = (new Wiki\CategorizedMySqlBulletpoints(
			new Fake\Identity(4),
			$connection,
			new Fake\Document(1)
		))->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpoint(
				new Access\MySqlIdentity(1, $connection),
				'first',
				new \Datetime('2000-01-01 01:01:01'),
				new Wiki\ConstantInformationSource(
					'wikipedie',
					2005,
					'facedown',
					new Wiki\MySqlInformationSource(1, $connection)
				),
				new Wiki\MySqlBulletpoint(1, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::equal(			
			new Wiki\ConstantBulletpoint(
				new Access\MySqlIdentity(2, $connection),
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

	public function testAdding() {
		$this->connection()->query('TRUNCATE bulletpoints');
		(new Wiki\CategorizedMySqlBulletpoints(
			new Fake\Identity(4),
			$this->connection(),
			new Fake\Document(1)
		))->add(
			'new content',
			new Fake\InformationSource(1, 'wikipeide', 2005, 'facedown')
		);
		Assert::same(
			[
				'user_id' => 4,
				'information_source_id' => 1,
				'document_id' => 1
			],
			$this->connection()->fetch(
				'SELECT user_id, information_source_id, document_id
				FROM bulletpoints
				WHERE content = "new content"'
			)
		);
	}

	/**
	* @throws \Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
	*/
	public function testAddingExistingOne() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO bulletpoints
			(ID, content, user_id, information_source_id, document_id, created_at)
			VALUES
			(1, "first", 1, 1, 1, "2000-01-01 01:01:01")'
		);
		(new Wiki\CategorizedMySqlBulletpoints(
			new Fake\Identity(4),
			$this->connection(),
			new Fake\Document(1)
		))->add(
			'first',
			new Fake\InformationSource(1, 'wikipeide', 2005, 'facedown')
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE information_sources');
		$connection->query('TRUNCATE bulletpoints');
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


(new CategorizedMySqlBulletpoints())->run();
