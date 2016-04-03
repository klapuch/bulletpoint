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

final class MySqlBulletpointRatings extends TestCase\Database {
    public function testIterating() {
        $connection = $this->preparedDatabase();
        $myself = new Fake\Identity(1);
        $ratings = (new Rating\MySqlBulletpointRatings(
            new Fake\Bulletpoints([1, 2, 3]),
            $myself,
            $connection
        ))->iterate();
        Assert::equal(
            new Rating\ConstantRating(
                1,
                1,
                new Rating\MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(3, $connection),
                    $myself,
                    $connection
                )
            ),
            $ratings->current()
        );

        $ratings->next();
        Assert::equal(
            new Rating\ConstantRating(
                0,
                1,
                new Rating\MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(2, $connection),
                    $myself,
                    $connection
                )
            ),
            $ratings->current()
        );
        $ratings->next();
        Assert::equal(
            new Rating\ConstantRating(
                2,
                0,
                new Rating\MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(1, $connection),
                    $myself,
                    $connection
                )
            ),
            $ratings->current()
        );
        $ratings->next();
        Assert::false($ratings->valid());

    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE bulletpoint_ratings');
        $connection->query(
            'INSERT INTO bulletpoint_ratings 
			(bulletpoint_id, rating, user_id)
			VALUES (1, "+1", 1), (1, "+1", 2),
			(2, "-1", 3),
			(3, "0", 4), (3, "+1", 5), (3, "-1", 6)'
        );
        return $connection;
    }
}


(new MySqlBulletpointRatings())->run();
