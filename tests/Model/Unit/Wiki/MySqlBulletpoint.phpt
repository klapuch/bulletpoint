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

final class MySqlBulletpoint extends TestCase\Database {
	public function testContent() {
		Assert::same(
			'first',
			(new Wiki\MySqlBulletpoint(
				1,
				$this->preparedBulletpoints()
			))->content()
		);
	}

	public function testDocument() {
		$connection = $this->preparedBulletpoints();
		Assert::equal(
			new Wiki\MySqlDocument(1, $connection),
			(new Wiki\MySqlBulletpoint(
				1,
				$this->preparedBulletpoints()
			))->document()
		);
	}	

	public function testDate() {
		Assert::equal(
			new \DateTimeImmutable('2000-01-01 01:01:01'),
			(new Wiki\MySqlBulletpoint(
				1,
				$this->preparedBulletpoints()
			))->date()
		);
	}

	public function testId() {
		Assert::same(
			2,
			(new Wiki\MySqlBulletpoint(
				2,
				new Fake\Database
			))->id()
		);
	}

	public function testAuthor() {
		$this->preparedBulletpoints();
		$this->preparedUsers();
		Assert::equal(
			new Access\MySqlIdentity(1, $this->connection()),
			(new Wiki\MySqlBulletpoint(
				1,
				$this->connection()
			))->author()
		);
	}

	public function testSource() {
		$connection = $this->preparedInformationSources();
		Assert::equal(
			new Wiki\MySqlInformationSource(1, $connection),
			(new Wiki\MySqlBulletpoint(
				1,
				$connection
			))->source()
		);
	}

	public function testEditing() {
		$connection = $this->preparedBulletpoints();
		(new Wiki\MySqlBulletpoint(1, $connection))->edit('newContent');
		Assert::same(
			[
				'content' => 'newContent',
				'created_at' => '2000-01-01 01:01:01'
			],
			$connection->fetch(
				'SELECT content, created_at
				FROM bulletpoints
				WHERE ID = 1'
			)
		);
	}

    /**
     * @throws \Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
     */
    public function testEditingToDuplication() {
        $connection = $this->preparedBulletpoints();
        (new Wiki\MySqlBulletpoint(1, $connection))->edit('second');
    }

	private function preparedInformationSources() {
		$connection = $this->connection();
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, author, `year`)
			VALUES (1, "wikipedie", "facedown", 2005),
			(2, "some book", "čapek", 1998)'
		);
		return $connection;
	}

	private function preparedBulletpoints() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO bulletpoints
			(ID, content, user_id, information_source_id, created_at, document_id)
			VALUES (1, "first", 1, 1, "2000-01-01 01:01:01", 1),
			(2, "second", 2, 2, "2000-01-01 01:01:01", 1)'
		);
		return $connection;
	}

	private function preparedUsers() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, role) VALUES (1, "user")'
		);
		return $connection;
	}
}


(new MySqlBulletpoint())->run();
