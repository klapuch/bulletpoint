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

final class ActivationMessage extends TestCase\Database {
	public function testSender() {
		Assert::equal(
			'bulletpoint <aktivace@bulletpoint.cz>',
			(new Email\ActivationMessage('foo@bar.cz', new Fake\Database))
			->sender()
		);
	}

	public function testRecipient() {
		Assert::same(
			'foo@bar.cz',
			(new Email\ActivationMessage('foo@bar.cz', new Fake\Database))
			->recipient()
		);
	}

	public function testSubject() {
		Assert::same(
			'Aktivace účtu na bulletpoint',
			(new Email\ActivationMessage('foo@bar.cz', new Fake\Database))
			->subject()
		);
	}

	public function testContent() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, code) VALUES (1, "123456")'
		);
		$connection->query(
			'INSERT INTO users (ID, email) VALUES (1, "foo@bar.cz")'
		);
		$connection->query(
			'INSERT INTO message_templates (message, designation)
			VALUES ("Ověřovací kód je: %s nebo %s", "activation")'
		);
		Assert::same(
			'Ověřovací kód je: 123456 nebo 123456',
			(new Email\ActivationMessage('foo@bar.cz', $connection))
			->content()
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE verification_codes');
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE message_templates');
		return $connection;
	}
}


(new ActivationMessage())->run();
