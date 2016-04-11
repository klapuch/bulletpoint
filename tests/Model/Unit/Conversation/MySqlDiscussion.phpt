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

	public function testComments() {
		$identity = new Fake\Identity(4, new Fake\Role('user'));
		$discussion = new Conversation\MySqlDiscussion(
			1,
			$identity,
			new Fake\Database
		);
		Assert::equal(
            new Conversation\InDiscussionMySqlComments(
                $discussion,
                $identity,
                new Fake\Database
            ),
            $discussion->comments()
        );
	}
}


(new MySqlDiscussion())->run();
