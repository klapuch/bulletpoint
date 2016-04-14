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
use Bulletpoint\Model\{
    Access, Wiki
};

require __DIR__ . '/../../../bootstrap.php';

final class MySqlUserBulletpointRatings extends TestCase\Database {
    public function testIterating() {
        $connection = $this->preparedDatabase();
        $myself = new Fake\Identity(1);
        $ratings = (new Rating\MySqlUserBulletpointRatings(
            new Fake\Bulletpoints([1, 2, 3]),
            $myself,
            $connection
        ))->iterate();
        Assert::equal(
            [
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(3, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    0,
                    1,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(2, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    1,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(1, $connection),
                        $myself,
                        $connection
                    )
                ),
            ],
            iterator_to_array($ratings)
        );
    }

    public function testIteratingWithoutIdentity() {
        $connection = $this->preparedDatabase();
        $myself = new Fake\Identity(0);
        $ratings = (new Rating\MySqlUserBulletpointRatings(
            new Fake\Bulletpoints([1, 2, 3]),
            $myself,
            $connection
        ))->iterate();
        Assert::equal(
            [
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(3, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(2, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(1, $connection),
                        $myself,
                        $connection
                    )
                ),
            ],
            iterator_to_array($ratings)
        );
    }

    public function testIteratingWithoutRating() {
        $connection = $this->preparedDatabase();
        $myself = new Fake\Identity(999);
        $ratings = (new Rating\MySqlUserBulletpointRatings(
            new Fake\Bulletpoints([1, 2, 3]),
            $myself,
            $connection
        ))->iterate();
        Assert::equal(
            [
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(3, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    1,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(2, $connection),
                        $myself,
                        $connection
                    )
                ),
                new Rating\ConstantRating(
                    0,
                    0,
                    new Rating\MySqlBulletpointRating(
                        new Wiki\MySqlBulletpoint(1, $connection),
                        $myself,
                        $connection
                    )
                ),
            ],
            iterator_to_array($ratings)
        );
    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE bulletpoint_ratings');
        $connection->query(
            'INSERT INTO bulletpoint_ratings 
			(bulletpoint_id, rating, user_id)
			VALUES (1, "+1", 1), (1, "+1", 2),
			(2, "-1", 1),
			(3, "0", 1), (3, "+1", 5), (3, "-1", 6), (2, "+1", 999)'
        );
        return $connection;
    }
}


(new MySqlUserBulletpointRatings())->run();
