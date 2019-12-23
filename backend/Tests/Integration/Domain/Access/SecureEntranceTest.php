<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Encryption;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class SecureEntranceTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testSuccessfulAuthenticatingWithExactlySameCredentials(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'role' => 'member']))->try();
		$user = (new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(true),
		))->enter(['login' => 'foo@bar.cz', 'password' => 'heslo']);
		Assert::same((string) $id, $user->id());
	}

	public function testExitingAndBecomingToGuest(): void {
		Assert::equal(
			new Access\Guest(),
			(new Access\SecureEntrance(
				$this->connection,
				new Encryption\FakeCipher(true),
			))->exit(),
		);
	}

	public function testSuccessfulAuthenticatingWithCaseInsensitiveEmail(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'role' => 'member']))->try();
		Assert::noError(function (): void {
			(new Access\SecureEntrance(
				$this->connection,
				new Encryption\FakeCipher(true),
			))->enter(['login' => 'FOO@bar.cz', 'password' => 'heslo']);
		});
	}

	public function testPassingWithStringObject(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'role' => 'member']))->try();
		Assert::noError(function (): void {
			(new Access\SecureEntrance(
				$this->connection,
				new Encryption\FakeCipher(true),
			))->enter(
				[
					'login' =>
					new class {
						public function __toString(): string {
							return 'foo@bar.cz';
						}
					},
					'password' =>
					new class {
						public function __toString(): string {
							return 'heslo';
						}
					},
				],
			);
		});
	}

	public function testAuthenticatingWithoutRehashing(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'password' => 'heslo', 'role' => 'member']))->try();
		$statement = $this->connection->prepare('SELECT password FROM users WHERE id = ?');
		$statement->execute([$id]);
		Assert::same('heslo', $statement->fetchColumn());
		$user = (new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(true, false),
		))->enter(['login' => 'foo@bar.cz', 'password' => 'heslo']);
		Assert::same((string) $id, $user->id());
		$statement->execute();
		Assert::same('heslo', $statement->fetchColumn());
	}

	/**
	 * @throws \UnexpectedValueException Email "foo@bar.cz" does not exist
	 */
	public function testThrowingOn3rdPartAuthentication(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'password' => null, 'facebook_id' => 123, 'role' => 'member']))->try();
		(new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(),
		))->enter(['login' => 'foo@bar.cz', 'password' => 'heslo']);
	}

	/**
	 * @throws \UnexpectedValueException Email "unknown@bar.cz" does not exist
	 */
	public function testThrowingOnAuthenticatingWithUnknownEmail(): void {
		(new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(),
		))->enter(['login' => 'unknown@bar.cz', 'password' => 'heslo']);
	}

	/**
	 * @throws \UnexpectedValueException Wrong password
	 */
	public function testThrowingOnAuthenticatingWithWrongPassword(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'role' => 'member']))->try();
		(new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(false),
		))->enter(['login' => 'foo@bar.cz', 'password' => '2heslo2']);
	}

	public function testAuthenticatingRehasingPassword(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz', 'password' => 'heslo', 'role' => 'member']))->try();
		$statement = $this->connection->prepare('SELECT password FROM users WHERE id = ?');
		$statement->execute([$id]);
		Assert::same('heslo', $statement->fetchColumn());
		$user = (new Access\SecureEntrance(
			$this->connection,
			new Encryption\FakeCipher(true, true),
		))->enter(['login' => 'foo@bar.cz', 'password' => 'heslo']);
		Assert::same((string) $id, $user->id());
		$statement->execute();
		Assert::same('secret', $statement->fetchColumn());
	}
}

(new SecureEntranceTest())->run();
