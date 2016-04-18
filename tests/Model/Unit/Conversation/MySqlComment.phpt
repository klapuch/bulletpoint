<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Conversation;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlComment extends TestCase\Database {
	public function testContent() {
		Assert::same(
			'great comment',
			(new Conversation\MySqlComment(
				1,
				new Fake\Identity(1),
				$this->preparedDatabase()
			))->content()
		);
	}

	public function testDate() {
		Assert::equal(
			new \DateTimeImmutable('2000-01-01 01:01:01'),
			(new Conversation\MySqlComment(
				1,
				new Fake\Identity(1),
				$this->preparedDatabase()
			))->date()
		);
	}

	public function testAuthor() {
        $connection = $this->preparedDatabase();
		Assert::equal(
			new Access\MySqlIdentity(1, $connection),
			(new Conversation\MySqlComment(
				1,
				new Fake\Identity(10, new Fake\Role('user')),
				$connection
			))->author()
		);
	}

	/**
	* @throws \Bulletpoint\Exception\AccessDeniedException Tento komentář jsi nenapsal
	*/
	public function testEditingForeignComment() {
		(new Conversation\MySqlComment(
			1,
			new Fake\Identity(10),
			$this->preparedDatabase()
		))->edit('Trying to edit foreign comment');
	}

	/**
	* @throws \Bulletpoint\Exception\AccessDeniedException Tento komentář již nemůže být upravován
	*/
	public function testEditingInvisibleComment() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comments');
		$connection->query(
			'INSERT INTO comments (user_id, visible) VALUES (1, 0)'
		);
		(new Conversation\MySqlComment(1, new Fake\Identity(1), $connection))
		->edit('blablah');
	}

	public function testErasingComment() {
		$connection = $this->preparedDatabase();
		(new Conversation\MySqlComment(1, new Fake\Identity(1), $connection))
		->erase();
		Assert::same(1, $connection->fetchColumn('SELECT COUNT(ID) FROM comments'));
		Assert::same(0, $connection->fetchColumn('SELECT visible FROM comments'));
	}

	public function testEditingOwnedComment() {
		$connection = $this->preparedDatabase();
		(new Conversation\MySqlComment(1, new Fake\Identity(1), $connection))
		->edit('Uaa new comment');
		Assert::same(
			'Uaa new comment',
			$connection->fetchColumn(
				'SELECT content FROM comments WHERE ID = 1'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comments');
		$connection->query('TRUNCATE users');
        $connection->query(
            'INSERT INTO users (ID, role) VALUES (1, "user")'
        );
		$connection->query(
			'INSERT INTO comments (ID, user_id, posted_at, content, document_id)
			VALUES (1, 1, "2000-01-01 01:01:01", "great comment", 6)'
		);
		return $connection;
	}
}


(new MySqlComment())->run();
