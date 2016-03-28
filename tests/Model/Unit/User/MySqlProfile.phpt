<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\User;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlProfile extends TestCase\Database {
	public function testComments() {
		Assert::same(
			2,
			(new User\MySqlProfile(
				new Fake\Identity(1),
				$this->preparedComments()
			))->comments()
		);
	}

	public function testBulletpoints() {
		Assert::same(
			2,
			(new User\MySqlProfile(
				new Fake\Identity(1),
				$this->preparedBulletpoints()
			))->bulletpoints()
		);
	}

	public function testDocuments() {
		Assert::same(
			2,
			(new User\MySqlProfile(
				new Fake\Identity(1),
				$this->preparedDocuments()
			))->documents()
		);
	}

	public function testOwner() {
		Assert::equal(
			new Fake\Identity(1),
			(new User\MySqlProfile(
				new Fake\Identity(1),
				new Fake\Database
			))->owner()
		);
	}
	
	private function preparedComments() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comments');
		$connection->query(
			'INSERT INTO comments (user_id) VALUES (1), (1), (2)'
		);
		return $connection;
	}

	private function preparedBulletpoints() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO bulletpoints (user_id, content, document_id) VALUES
            (1, "a", 1), (1, "b", 2), (2, "c", 3)'
		);
		return $connection;
	}

	private function preparedDocuments() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		$connection->query(
			'INSERT INTO documents (user_id, title) VALUES
            (1, "a"), (1, "b"), (2, "c")'
		);
		return $connection;
	}
}


(new MySqlProfile())->run();
