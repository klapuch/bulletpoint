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

final class OwnedMySqlPunishments extends TestCase\Database {
	public function testIterating() {
        $connection = $this->preparedDatabase();
        $connection->query('TRUNCATE users');
        $connection->query(
			'INSERT INTO banned_users (user_id, expiration, reason, canceled)
			VALUES
			(2, "2100-01-01 12:01:01", "rude", 1),
			(2, "2099-01-01 12:01:01", "rude", 0),
			(3, "1999-01-01 12:01:01", "rude", 0)'
		);
        $connection->query(
			'INSERT INTO users (ID, role, username)
			VALUES (2, "user", "cucak")'
		);
        $rows = (new Constraint\OwnedMySqlPunishments(
			new Fake\Identity(2),
			$connection,
			new Fake\Punishments(new Fake\Identity, new Fake\Database)
		))->iterate();
        Assert::equal(
			new Constraint\ConstantPunishment(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'user',
						new Access\MySqlRole(2, $connection)
					),
					'cucak'
				),
				'rude',
				new \Datetime('2099-01-01 12:01:01'),
				new Constraint\MySqlPunishment(2, $connection)
			),
			$rows->current()
		);
        $rows->next();
        Assert::equal(
            new Constraint\ConstantPunishment(
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
                new Constraint\MySqlPunishment(1, $connection)
            ),
            $rows->current()
        );
		$rows->next();
		Assert::false($rows->valid());
	}

    /**
     * @throws \LogicException Nemůžeš potrestat sám sebe
     */
    public function testPunishingMyself() {
        (new Constraint\OwnedMySqlPunishments(
            new Fake\Identity(1),
            new Fake\Database,
            new Fake\Punishments(new Fake\Identity, new Fake\Database)
        ))->punish(
            new Fake\Identity(1),
            new \Datetime('2100-01-01 12:00:00'),
            'rude'
        );
    }

    public function testPunishing() {
        (new Constraint\OwnedMySqlPunishments(
            new Fake\Identity(1),
            new Fake\Database,
            new Fake\Punishments(new Fake\Identity(1), new Fake\Database)
        ))->punish(
            new Fake\Identity(2),
            new \Datetime('2100-01-01 12:00:00'),
            'rude'
        );
        Assert::true(true);
    }

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE banned_users');
		return $connection;
	}
}


(new OwnedMySqlPunishments())->run();
