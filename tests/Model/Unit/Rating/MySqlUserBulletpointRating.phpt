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

final class MySqlUserBulletpointRating extends TestCase\Database {
	public function testPros() {
		$connection = $this->preparedRating();
		Assert::same(
			1,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(1),
				$connection,
				new Fake\Rating
			))->pros()
		);
		Assert::same(
			0,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(2),
				$connection,
				new Fake\Rating
			))->pros()
		);		
	}

	public function testCons() {
		$connection = $this->preparedRating();
		Assert::same(
			0,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(1),
				$connection,
				new Fake\Rating
			))->cons()
		);			
		Assert::same(
			1,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(2),
				$connection,
				new Fake\Rating
			))->cons()
		);
	}

	public function testRatingForNeverVotedUser() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_ratings');
		Assert::same(
			0,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(3),
				$connection,
				new Fake\Rating
			))->cons()
		);
		Assert::same(
			0,
			(new Rating\MySqlUserBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(3),
				$connection,
				new Fake\Rating
			))->pros()
		);
	}

	private function preparedRating() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_ratings');
		$connection->query(
			'INSERT INTO bulletpoint_ratings 
			(bulletpoint_id, user_id, rating)
			VALUES (1, 1, "+1"), (1, 2, "-1")'
		);
		return $connection;
	}
}


(new MySqlUserBulletpointRating())->run();
