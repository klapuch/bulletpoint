<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class RegisteredUserTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testInfoAboutRegisteredUser(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'role' => 'member']))->try();
		$user = new Access\RegisteredUser($id, $this->connection);
		Assert::same((string) $id, $user->id());
		['email' => $email, 'role' => $role] = $user->properties();
		Assert::same($email, 'foo@bar.cz');
		Assert::same($role, 'member');
	}

	public function testEditingUser(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['username' => 'abc']))->try();
		$user = new Access\RegisteredUser($id, $this->connection);
		['username' => $username] = $user->properties();
		Assert::same($username, 'abc');
		$user->edit(['username' => 'COOL']);
		['username' => $username] = $user->properties();
		Assert::same($username, 'COOL');
	}

	public function testPassingOnSameUsername(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['username' => 'abc']))->try();
		Assert::noError(function () use ($id): void {
			(new Access\RegisteredUser($id, $this->connection))->edit(['username' => 'abc']);
		});
	}

	public function testThrowingOnEditingToExistingUsername(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['username' => 'abc']))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['username' => 'taken']))->try();
		Assert::exception(function () use ($id): void {
			(new Access\RegisteredUser($id, $this->connection))->edit(['username' => 'taken']);
		}, \UnexpectedValueException::class, 'Username "taken" already exists.');
	}

	public function testThrowingOnNotRegisteredUser(): void {
		$user = new Access\RegisteredUser(1, $this->connection);
		Assert::exception(static function() use ($user): void {
			$user->id();
		}, \UnexpectedValueException::class, 'The user has not been registered yet');
		Assert::exception(static function() use ($user): void {
			$user->properties();
		}, \UnexpectedValueException::class, 'The user has not been registered yet');
		Assert::exception(static function() use ($user): void {
			$user->edit(['username' => '']);
		}, \UnexpectedValueException::class, 'The user has not been registered yet');
	}
}

(new RegisteredUserTest())->run();
