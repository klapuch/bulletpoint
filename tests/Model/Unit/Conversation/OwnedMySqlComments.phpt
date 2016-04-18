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

final class OwnedMySqlComments extends TestCase\Database {
    public function testIterating() {
        $connection = $this->preparedDatabase();
        $owner = new Fake\Identity(1, new Fake\Role('member'));
        $discussion = new Conversation\OwnedMySqlComments(
            $owner,
            $connection
        );
        $comments = $discussion->iterate();
        Assert::same(1, $discussion->count());
        Assert::equal(
            new Conversation\ConstantComment(
                $owner,
                'first',
                new \DateTimeImmutable('2000-01-01 01:01:01'),
                1,
                new Conversation\MySqlComment(2, $owner, $connection)
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


(new OwnedMySqlComments())->run();
