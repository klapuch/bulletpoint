<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Report;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlUnsettledComplaints extends TestCase\Database {
    public function testIterating() {
        $connection = $this->preparedDatabase();
        $myself = new Fake\Identity(1);
        $complaints = (new Report\MySqlUnsettledComplaints(
            $myself,
            $connection
        ))->iterate(new Report\Target(1));
        Assert::equal(
            new Report\ConstantComplaint(
                new Access\ConstantIdentity(
                    1,
                    new Access\ConstantRole(
                        'member',
                        new Access\MySqlRole(1, $connection)
                    ),
                    'face'
                ),
                new Report\Target(1),
                'Vulgarita',
                new Report\MySqlComplaint(1, $myself, $connection)
            ),
            $complaints->current()
        );
        $complaints->next();
        Assert::false($complaints->valid());
    }

    public function testIteratingForVisibleComment() {
        $connection = $this->preparedDatabase();
        $connection->query(
            'INSERT INTO comments(ID, visible) VALUES (2, 0)'
        );
        $connection->query(
            'INSERT INTO comment_complaints
			(comment_id, settled, user_id, reason)
			VALUES (2, 0, 1, "vulgarita")'
        );
        Assert::false((new Report\MySqlUnsettledComplaints(
            new Fake\Identity(1),
            $connection
        ))->iterate(new Report\Target(2))->valid());
    }

    public function testSettling() {
        $connection = $this->preparedDatabase();
        (new Report\MySqlUnsettledComplaints(
            new Fake\Identity(1),
            $connection
        ))->settle(new Report\Target(1));
        Assert::same(
            1,
            $connection->fetchColumn('SELECT settled FROM comment_complaints')
        );
    }

    public function testComplaining() {
        $connection = $this->preparedDatabase();
        $identity = new Fake\Identity(2);
        $complaint = (new Report\MySqlUnsettledComplaints($identity, $connection))
            ->complain(new Report\Target(6), 'vulgarita');
        Assert::equal(
            new Report\MySqlComplaint(2, $identity, $connection),
            $complaint
        );
        Assert::same(
            [
                'comment_id' => 6,
                'reason' => 'Vulgarita'
            ],
            $connection->fetch(
                'SELECT comment_id, reason
				FROM comment_complaints WHERE ID = 2'
            )
        );

    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE comment_complaints');
        $connection->query('TRUNCATE users');
        $connection->query('TRUNCATE comments');
        $connection->query(
            'INSERT INTO comments(ID, visible) VALUES (1, 1)'
        );
        $connection->query(
            'INSERT INTO comment_complaints
			(comment_id, settled, user_id, reason)
			VALUES (1, 0, 1, "vulgarita")'
        );
        $connection->query(
            'INSERT INTO users (role, username, email) VALUES
            ("member", "face", "e1")'
        );
        return $connection;
    }
}


(new MySqlUnsettledComplaints())->run();
