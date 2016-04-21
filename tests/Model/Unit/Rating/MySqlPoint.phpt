<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Rating;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlPoint extends TestCase\Database {
    public function testOwner() {
        $connection = $this->preparedDatabase();
        Assert::equal(
            new Access\MySqlIdentity(1, $connection),
            (new Rating\MySqlPoint(3, $connection))->voter()
        );
    }

    public function testPoint() {
        $connection = $this->preparedDatabase();
        Assert::same(
            1,
            (new Rating\MySqlPoint(1, $connection))->value()
        );
        Assert::same(
            0,
            (new Rating\MySqlPoint(2, $connection))->value()
        );
        Assert::same(
            -1,
            (new Rating\MySqlPoint(3, $connection))->value()
        );
    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE bulletpoint_ratings');
        $connection->query(
            'INSERT INTO bulletpoint_ratings 
			(ID, bulletpoint_id, user_id, point)
			VALUES (1, 1, 1, +1), (2, 2, 1, 0), (3, 3, 1, -1)'
        );
        return $connection;
    }
}


(new MySqlPoint())->run();
