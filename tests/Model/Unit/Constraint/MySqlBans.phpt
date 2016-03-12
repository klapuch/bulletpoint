<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\{Access, Constraint};
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlBans extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO banned_users (user_id, expiration, reason)
			VALUES
			(2, "2100-01-01 12:01:01", "rude"),
			(2, "1999-01-01 12:01:01", "rude")'
		);
		$connection->query(
			'INSERT INTO users (ID, role, username)
			VALUES (2, "user", "cucak")'
		);
		$rows = (new Constraint\MySqlBans(
			new Fake\Identity(1),
			$connection
		))->iterate();
		Assert::equal(
			new Constraint\ConstantBan(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'user',
						new Access\MySqlRole(2, $connection)
					),
					'cucak'
				),
				'rude',
				new \Datetime('2100-01-01 12:01:01'),
				new Constraint\MySqlBan(1, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	public function testBaning() {
		(new Constraint\MySqlBans(
			new Fake\Identity(1),
			$this->preparedDatabase()
		))->give(
			new Fake\Identity(2),
			new \Datetime('2100-01-01 12:00:00'),
			'rude'
		);
		Assert::same(
			[
				'author_id' => 1,
				'expiration' => '2100-01-01 12:00:00',
				'reason' => 'rude'
			],
			$this->connection()->fetch(
				'SELECT author_id, expiration, reason
				FROM banned_users
				WHERE user_id = 2'
			)
		);
	}

	/**
	* @throws \LogicException Ban nemůžeš udělit sám sobě.
	*/
	public function testBaningMyself() {
		(new Constraint\MySqlBans(
			new Fake\Identity(1),
			new Fake\Database
		))->give(
			new Fake\Identity(1),
			new \Datetime('2100-01-01 12:00:00'),
			'rude'
		);
	}

	public function testBaningAlreadyBannedUser() {
        $connection = $this->preparedDatabase();
		$bans = new Constraint\MySqlBans(
			new Fake\Identity(1),
			$connection
		);
		$sinner = new Fake\Identity(2);
		$bans->give($sinner, new \Datetime('tomorrow'), 'rude');
        Assert::same(1, iterator_count($bans->iterate()));
        $bans->give($sinner, new \Datetime('+5 months'), 'idiot');
        Assert::same(2, iterator_count($bans->iterate()));
	}

	public function testBaningUserWithCanceledOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO banned_users
			(user_id, canceled, expiration, reason, author_id)
			VALUES (2, 1, "2200-01-01 01:01:01", "rude", 1)'
		);
		(new Constraint\MySqlBans(
			new Fake\Identity(1),
			$connection
		))->give(new Fake\Identity(2), new \Datetime('2100-01-01 12:00:00'));
		Assert::same(
			[
				'author_id' => 1,
				'expiration' => '2100-01-01 12:00:00',
				'reason' => null,
			],
			$this->connection()->fetch(
				'SELECT author_id, expiration, reason
				FROM banned_users
				WHERE user_id = 2 AND canceled = 0'
			)
		);
	}

	/**
	* @throws \LogicException Ban můžeš dát pouze na budoucí období.
	*/
	public function testGivingBanWithExpiredDate() {
		(new Constraint\MySqlBans(
			new Fake\Identity(1),
			$this->preparedDatabase()
		))->give(
			new Fake\Identity(2),
			new \Datetime('yesterday')
		);
	}

	public function testBaningUserWithExpiredOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO banned_users (user_id, expiration, reason)
			VALUES (2, "NOW() - INTERVAL 2 DAY", "idiot")'
		);
		$ban = new Constraint\MySqlBans(
			new Fake\Identity(1),
			$connection
		);
		$ban->give(
			new Fake\Identity(2),
			new \Datetime('2100-01-01 12:00:00'),
			"rude"
		);
		Assert::same(
			[
				'author_id' => 1,
				'expiration' => '2100-01-01 12:00:00',
				'reason' => 'rude'
			],
			$this->connection()->fetch(
				'SELECT author_id, expiration, reason
				FROM banned_users
				WHERE user_id = 2
				ORDER BY expiration DESC'
			)
		);
	}

	public function testBanForConreteUser() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO banned_users (user_id, expiration, reason)
			VALUES (2, "2222-01-01 12:01:01", "rude")'
		);
		Assert::equal(
			new Constraint\ConstantBan(
			new Access\MySqlIdentity(2, $connection),
			'rude',
			new \Datetime('2222-01-01 12:01:01'),
			new Constraint\MySqlBan(1, $connection)
		), (new Constraint\MySqlBans(
				new Fake\Identity(1),
				$connection
			))->byIdentity(new Fake\Identity(2))
		);
	}

	public function testBanFromPoliteUser() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO banned_users (user_id, expiration, reason, canceled)
			VALUES (2, "2222-01-01 12:01:01", "idiot", 1)'
		);
		$ban = (new Constraint\MySqlBans(new Fake\Identity(1), $connection))
		->byIdentity(new Fake\Identity(2));
		Assert::equal(
			new Constraint\ConstantBan(
				new Access\MySqlIdentity(0, $connection),
				'',
				new \Datetime(),
				new Constraint\MySqlBan(0, $connection)
			), $ban
		);
		Assert::true($ban->expired());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE banned_users');
		return $connection;
	}
}


(new MySqlBans())->run();
