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
		Assert::same(
			2,
			(new Rating\MySqlBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(1),
				$this->preparedDatabase()
			))->pros()
		);
	}

	public function testCons() {
		Assert::same(
			1,
			(new Rating\MySqlBulletpointRating(
				new Fake\Bulletpoint(1),
				new Fake\Identity(1),
				$this->preparedDatabase()
			))->cons()
		);
	}

	public function testIncrementation() {
		$rating = new Rating\MySqlBulletpointRating(
			new Fake\Bulletpoint(1),
			new Fake\Identity(4),
			$this->preparedDatabase()
		);
		Assert::same(2, $rating->pros());
		$rating->increment();
		Assert::same(3, $rating->pros());
	}

	public function testDecrementation() {
		$rating = new Rating\MySqlBulletpointRating(
			new Fake\Bulletpoint(1),
			new Fake\Identity(4),
			$this->preparedDatabase()
		);
		Assert::same(1, $rating->cons());
		$rating->decrement();
		Assert::same(2, $rating->cons());
	}

	public function testUpdatingOnRatingChange() {
		$rating = new Rating\MySqlBulletpointRating(
			new Fake\Bulletpoint(1),
			new Fake\Identity(1),
			$this->preparedDatabase()
		);
		Assert::same(2, $rating->pros());
		Assert::same(1, $rating->cons());
		$rating->decrement();
		Assert::same(1, $rating->pros());
		Assert::same(2, $rating->cons());
	}

	public function testToggling() {
		$rating = new Rating\MySqlBulletpointRating(
			new Fake\Bulletpoint(1),
			new Fake\Identity(1),
			$this->preparedDatabase()
		);
		Assert::same(2, $rating->pros());
		Assert::same(1, $rating->cons());
		$rating->increment();
		Assert::same(1, $rating->pros());
		Assert::same(1, $rating->cons());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_ratings');
		$connection->query(
			'INSERT INTO bulletpoint_ratings 
			(bulletpoint_id, user_id, rating)
			VALUES (1, 1, "+1"), (1, 2, "+1"), (1, 3, "-1")'
		);
		return $connection;
	}
}


(new MySqlBulletpointRating())->run();
