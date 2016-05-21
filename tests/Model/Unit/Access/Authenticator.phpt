<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Access;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class Authenticator extends TestCase\Database {
	public function testSuccessfulLogin() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 1)'
		);
		$identity = (new Access\Authenticator(
			$connection,
			new Fake\Cipher
		))->authenticate(['facedown', '123456']);
        Assert::same(
            '123456',
            $connection->fetchColumn('SELECT `password` FROM users WHERE ID = 1')
        );
        Assert::same($identity->id, 1);
		Assert::same($identity->roles, ['member']);
		Assert::same($identity->username, 'facedown');
	}

    public function testRehashing() {
        $connection = $this->preparedDatabase();
        $connection->query(
            'INSERT INTO verification_codes (user_id, used) VALUES (1, 1)'
        );
        $identity = (new Access\Authenticator(
            $connection,
            new Fake\Cipher($decrypted = true, $deprecated = true)
        ))->authenticate(['facedown', '123456']);
        Assert::same(
            'encrypted',
            $connection->fetchColumn('SELECT `password` FROM users WHERE username = "facedown"')
        );
        Assert::same($identity->id, 1);
        Assert::same($identity->roles, ['member']);
        Assert::same($identity->username, 'facedown');
    }

	/**
	* @throws \Nette\Security\AuthenticationException Účet není aktivován
	*/
	public function testLoginWithoutAccountActivation() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 0)'
		);
		(new Access\Authenticator(
			$connection,
			new Fake\Cipher
		))->authenticate(['facedown', '123456']);
	}

	/**
	* @throws \Nette\Security\AuthenticationException Uživatel neexistuje
	*/
	public function testWrongUsername() {
		(new Access\Authenticator(
            $this->preparedDatabase(),
			new Fake\Cipher
		))->authenticate(['foooooo', '123456']);
	}

	/**
	* @throws \Nette\Security\AuthenticationException Nesprávné heslo
	*/
	public function testWrongPassword() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO verification_codes (user_id, used) VALUES (1, 1)'
		);
		(new Access\Authenticator(
			$connection,
			new Fake\Cipher($decrypted = false)
		))->authenticate(['facedown', '123456789']);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE verification_codes');
		$connection->query(
			'INSERT INTO users (ID, username, password, email, role) 
			VALUES (1, "facedown", "123456", "facedown@email.cz", "member")'
		);
		return $connection;
	}
}


(new Authenticator())->run();
