<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Email;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class ForgottenPasswordMessage extends TestCase\Database {
	public function testSender() {
		Assert::equal(
			'bulletpoint <zapomenute-heslo@bulletpoint.cz>',
			(new Email\ForgottenPasswordMessage('foo@bar.cz', new Fake\Database))
			->sender()
		);
	}

	public function testRecipient() {
		Assert::same(
			'foo@bar.cz',
			(new Email\ForgottenPasswordMessage('foo@bar.cz', new Fake\Database))
			->recipient()
		);
	}

	public function testSubject() {
		Assert::same(
			'Zapomenuté heslo na bulletpoint',
			(new Email\ForgottenPasswordMessage('foo@bar.cz', new Fake\Database))
			->subject()
		);
	}

	public function testContent() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO forgotten_passwords (user_id, reminder, reminded_at)
			VALUES (1, "123456", NOW() - INTERVAL 2 DAY),
			(1, "xxxx", NOW() - INTERVAL 1 HOUR)'
		);		
		$connection->query(
			'INSERT INTO users (ID, email) VALUES (1, "foo@bar.cz")'
		);
		$connection->query(
			'INSERT INTO message_templates (message, designation)
			VALUES ("Kód pro obnovu hesla je: %s nebo %s", "forgotten-password")'
		);
		Assert::same(
			'Kód pro obnovu hesla je: xxxx nebo xxxx',
			(new Email\ForgottenPasswordMessage('foo@bar.cz', $connection))
			->content()
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE forgotten_passwords');
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE message_templates');
		return $connection;
	}
}


(new ForgottenPasswordMessage())->run();
