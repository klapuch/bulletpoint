<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;
use Bulletpoint\Fake;
use Bulletpoint\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class ReminderRule extends TestCase\Database {
	protected function invalidCodes() {
		return [
			[0],
			['FoooBar'],
            [''],
            [' '],
		];
	}

	/**
	* @dataProvider invalidCodes
	*/
	public function testInvalidCodes($code) {
		Assert::exception(function() use ($code) {
			(new Constraint\ReminderRule(new Fake\Database))->isSatisfied($code);
		}, 'Bulletpoint\Exception\FormatException', 'Obnovovací kód nemá správný formát');
	}

	/**
	* @throws \Bulletpoint\Exception\DuplicateException Obnovovací kód byl již využit
	*/
	public function testUsedCode() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (reminder, used)
			VALUES ("e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363", 1)'
		);
		(new Constraint\ReminderRule($connection))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
	}

	/**
	* @throws \Bulletpoint\Exception\ExistenceException Obnovovací kód pozbyl platnosti 24 hodin
	*/
	public function testExpiredCode() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (reminder, reminded_at)
			VALUES ("e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363", NOW() - INTERVAL 25 HOUR)'
		);
		(new Constraint\ReminderRule($connection))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
	}

	/**
	* @throws \Bulletpoint\Exception\ExistenceException Obnovovací kód neexistuje
	*/
	public function testUnknownCode() {
		$connection = $this->preparedDatabase();
		(new Constraint\ReminderRule($connection))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
	}

	public function testValidCode() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (reminder, reminded_at)
			VALUES ("e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4369", NOW() - INTERVAL 4 DAY)'
		);		
		$connection->query(
			'INSERT INTO forgotten_passwords (reminder, reminded_at)
			VALUES ("e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363", NOW() - INTERVAL 1 HOUR)'
		);
		(new Constraint\ReminderRule($connection))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
		Assert::true(true);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE forgotten_passwords');
		return $connection;
	}
}


(new ReminderRule())->run();
