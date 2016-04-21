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

final class MySqlBulletpointRating extends TestCase\Database {
	public function testPros() {
        $connection = $this->preparedDatabase();
		Assert::equal(
            [
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(1, $connection),
                    new Rating\MySqlPoint(1, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(2, $connection),
                    new Rating\MySqlPoint(2, $connection)
                ),
            ],
			iterator_to_array((new Rating\MySqlBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(1),
				$connection
			))->points())
		);
	}

	public function testCons() {
        $connection = $this->preparedDatabase();
		Assert::equal(
			[
                new Rating\ConstantPoint(
                    -1,
                    new Access\MySqlIdentity(3, $connection),
                    new Rating\MySqlPoint(3, $connection)
                ),
            ],
            iterator_to_array((new Rating\MySqlBulletpointRating(
                new Fake\Bulletpoint(3),
                new Fake\Identity(1),
                $connection
            ))->points())
		);
	}

	public function testRating() {
        $connection = $this->preparedDatabase();
		$rating = new Rating\MySqlBulletpointRating(
			new Fake\Bulletpoint(1),
			new Fake\Identity(4),
			$connection
		);
        Assert::equal(
            [
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(1, $connection),
                    new Rating\MySqlPoint(1, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(2, $connection),
                    new Rating\MySqlPoint(2, $connection)
                ),
            ],
            iterator_to_array($rating->points())
        );
		$rating->increase();
        Assert::equal(
            [
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(1, $connection),
                    new Rating\MySqlPoint(1, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(2, $connection),
                    new Rating\MySqlPoint(2, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(4, $connection),
                    new Rating\MySqlPoint(5, $connection)
                ),
            ],
            iterator_to_array($rating->points())
        );
        $rating->decrease();
        Assert::equal(
            [
                new Rating\ConstantPoint(
                    -1,
                    new Access\MySqlIdentity(4, $connection),
                    new Rating\MySqlPoint(5, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(1, $connection),
                    new Rating\MySqlPoint(1, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(2, $connection),
                    new Rating\MySqlPoint(2, $connection)
                ),
            ],
            iterator_to_array($rating->points())
        );
        $rating->decrease();
        Assert::equal(
            [
                new Rating\ConstantPoint(
                    0,
                    new Access\MySqlIdentity(4, $connection),
                    new Rating\MySqlPoint(5, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(1, $connection),
                    new Rating\MySqlPoint(1, $connection)
                ),
                new Rating\ConstantPoint(
                    1,
                    new Access\MySqlIdentity(2, $connection),
                    new Rating\MySqlPoint(2, $connection)
                ),
            ],
            iterator_to_array($rating->points())
        );
	}

    public function testNoPoint() {
        $connection = $this->preparedDatabase();
        Assert::equal(
            [
                new Rating\ConstantPoint(
                    0,
                    new Access\MySqlIdentity(4, $connection),
                    new Rating\MySqlPoint(4, $connection)
                ),
            ],
            iterator_to_array((new Rating\MySqlBulletpointRating(
                new Fake\Bulletpoint(4),
                new Fake\Identity(1),
                $connection
            ))->points())
        );
    }

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_ratings');
		$connection->query(
			'INSERT INTO bulletpoint_ratings 
			(ID, bulletpoint_id, user_id, point)
			VALUES (1, 1, 1, 1), (2, 1, 2, 1), (3, 3, 3, -1), (4, 4, 4, 0)'
		);
		return $connection;
	}
}


(new MySqlBulletpointRating())->run();
