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

final class MySqlDiscussion extends TestCase\Database {
	public function testContributing() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comments');
		(new Conversation\MySqlDiscussion(
			1,
			new Fake\Identity(4, new Fake\Role('member')),
			$connection
		))->post('new comment...');
		Assert::same(
			[
				'user_id' => 4,
				'content' => 'new comment...',
				'document_id' => 1,
			],
			$connection->fetch(
				'SELECT user_id, content, document_id
				FROM comments WHERE ID = 1'
			)
		);
	}

	public function testContributions() {
		$connection = $this->preparedDatabase();
		$identity = new Fake\Identity(4, new Fake\Role('user'));
		$discussion = new Conversation\MySqlDiscussion(
			1,
			$identity,
			$connection
		);
		$comments = $discussion->comments();
		Assert::equal(
			new Conversation\ConstantComment(
				new Access\ConstantIdentity(
					1,
					new Access\ConstantRole(
						'member',
						new Access\MySqlRole(1, $connection)
					),
					'cucak'
				),
				'first',
				new \Datetime('2000-01-01 01:01:01'),
				1,
				new Conversation\MySqlComment(2, $identity, $connection)
			),
			$comments->current()
		);
		$comments->next();
		Assert::equal(
			new Conversation\ConstantComment(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'administrator',
						new Access\MySqlRole(2, $connection)
					),
					'facedown'
				),
				'second',
				new \Datetime('1999-09-09 09:09:09'),
				1,
				new Conversation\MySqlComment(1, $identity, $connection)
			),
			$comments->current()
		);
		$comments->next();
		Assert::false($comments->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comments');
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, role, username, email)
			VALUES
			(1, "member", "cucak", "e"), (2, "administrator", "facedown", "e2")'
		);
		$connection->query(
			'INSERT INTO comments (ID, user_id, content, posted_at, document_id)
			VALUES
			(1, 2, "second", "1999-09-09 09:09:09", 1),
			(2, 1, "first", "2000-01-01 01:01:01", 1)'
		);
		return $connection;
	}
}


(new MySqlDiscussion())->run();
