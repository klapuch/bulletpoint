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

final class ActualMySqlPunishments extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
        $connection->query('TRUNCATE users');
        $connection->query(
            'INSERT INTO users (ID, username, role) VALUES (2, "xx", "member")'
        );
		$connection->query(
			'INSERT INTO punishments (sinner_id, expiration, reason, forgiven)
			VALUES
			(2, "2100-01-01 12:01:01", "rude", 0),
			(2, "2100-01-01 12:01:01", "rude", 1),
			(2, "1999-01-01 12:01:01", "rude", 0)'
		);
		$rows = (new Constraint\ActualMySqlPunishments(
			new Fake\Identity(1),
			$connection
		))->iterate();
        Assert::equal(
            new Constraint\ConstantPunishment(
                new Access\ConstantIdentity(
                    2,
                    new Access\MySqlRole(2, $connection),
                    'xx'
                ),
                'rude',
                new \Datetime('2100-01-01 12:01:01'),
                new Constraint\MySqlPunishment(1, $connection)
            ),
            $rows->current()
        );
        $rows->next();
        Assert::false($rows->valid());
	}

	public function testPunishing() {
        $connection = $this->preparedDatabase();
		(new Constraint\ActualMySqlPunishments(
			new Fake\Identity(1),
			$connection
		))->punish(
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
			$connection->fetch(
				'SELECT author_id, expiration, reason
				FROM punishments
				WHERE sinner_id = 2'
			)
		);
	}

	public function testPunishingAlreadyPunishedUser() {
        $connection = $this->preparedDatabase();
		$punishments = new Constraint\ActualMySqlPunishments(
			new Fake\Identity(1),
			$connection
		);
		$sinner = new Fake\Identity(2);
		$punishments->punish($sinner, new \Datetime('tomorrow'), 'rude');
        $punishments->punish($sinner, new \Datetime('+5 months'), 'idiot');
        $punishments->punish($sinner, new \Datetime('+5 months'), 'idiot');
        Assert::same(3, $connection->fetchColumn(
            'SELECT COUNT(ID) FROM punishments WHERE sinner_id = 2'
        ));
	}

	public function testPunishingUserWithForgivenOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO punishments
			(sinner_id, forgiven, expiration, reason, author_id)
			VALUES (2, 1, "2200-01-01 01:01:01", "rude", 1)'
		);
		(new Constraint\ActualMySqlPunishments(
			new Fake\Identity(1),
			$connection
		))->punish(
			new Fake\Identity(2),
			new \Datetime('2100-01-01 12:00:00'),
			'rude'
		);
		Assert::same(
			[
				'author_id' => 1,
				'expiration' => '2100-01-01 12:00:00',
				'reason' => 'rude',
			],
			$connection->fetch(
				'SELECT author_id, expiration, reason
				FROM punishments
				WHERE sinner_id = 2 AND forgiven = 0'
			)
		);
	}

	/**
	* @throws \LogicException Trest smí být udělen pouze na budoucí období
	*/
	public function testPunishingWithPastDate() {
		(new Constraint\ActualMySqlPunishments(
			new Fake\Identity(1),
			$this->preparedDatabase()
		))->punish(
			new Fake\Identity(2),
			new \Datetime('yesterday'),
			'rude'
		);
	}

	public function testPunishingUserWithExpiredOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO punishments (sinner_id, expiration, reason)
			VALUES (2, "NOW() - INTERVAL 2 DAY", "idiot")'
		);
        (new Constraint\ActualMySqlPunishments(
            new Fake\Identity(1),
            $connection
        ))->punish(
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
			$connection->fetch(
				'SELECT author_id, expiration, reason
				FROM punishments
				WHERE sinner_id = 2
				ORDER BY expiration DESC'
			)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE punishments');
		return $connection;
	}
}


(new ActualMySqlPunishments())->run();
