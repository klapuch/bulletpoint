<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Api\Endpoint\Tokens;

use Bulletpoint\Api\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Output;
use Klapuch\Storage;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class PostTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz']))->try();
		(new Storage\NativeQuery($this->connection, 'UPDATE access.verification_codes SET used_at = NOW()'))->execute();
		$response = (new Endpoint\Tokens\Post(
			new Application\FakeRequest(
				new Output\FakeFormat(
					json_encode(['login' => 'foo@bar.cz', 'password' => '123'], JSON_THROW_ON_ERROR),
				),
			),
			$this->connection,
			new Encryption\FakeCipher(true),
		))->response([]);
		$access = json_decode($response->body()->serialization(), true);
		Assert::true(isset($access['token']));
		Assert::true(isset($access['expiration']));
		Assert::same(HTTP_CREATED, $response->status());
	}

	public function test400OnBadInput(): void {
		Assert::exception(function () {
			(new Endpoint\Tokens\Post(
				new Application\FakeRequest(
					new Output\FakeFormat(json_encode(['foo' => 'bar'], JSON_THROW_ON_ERROR)),
				),
				$this->connection,
				new Encryption\FakeCipher(true),
			))->response([]);
		}, \UnexpectedValueException::class, 'The property login is required');
	}

	public function test403OnUnknownLogin(): void {
		Assert::exception(function () {
			(new Endpoint\Tokens\Post(
				new Application\FakeRequest(
					new Output\FakeFormat(
						json_encode(['login' => 'foo@baz.cz', 'password' => '123'], JSON_THROW_ON_ERROR),
					),
				),
				$this->connection,
				new Encryption\FakeCipher(false),
			))->response([]);
		}, \UnexpectedValueException::class, 'Email "foo@baz.cz" does not exist', HTTP_FORBIDDEN);
	}

	public function test403OnWrongPassword(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz']))->try();
		(new Storage\NativeQuery($this->connection, 'UPDATE access.verification_codes SET used_at = NOW()'))->execute();
		Assert::exception(function () {
			(new Endpoint\Tokens\Post(
				new Application\FakeRequest(
					new Output\FakeFormat(
						json_encode(['login' => 'foo@bar.cz', 'password' => '123'], JSON_THROW_ON_ERROR),
					),
				),
				$this->connection,
				new Encryption\FakeCipher(false),
			))->response([]);
		}, \UnexpectedValueException::class, 'Wrong password', HTTP_FORBIDDEN);
	}

	public function test403OnNotVerifiedCode(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'users', ['email' => 'foo@bar.cz']))->try();
		Assert::exception(function () {
			(new Endpoint\Tokens\Post(
				new Application\FakeRequest(
					new Output\FakeFormat(
						json_encode(['login' => 'foo@bar.cz', 'password' => '123'], JSON_THROW_ON_ERROR),
					),
				),
				$this->connection,
				new Encryption\FakeCipher(true),
			))->response([]);
		}, \UnexpectedValueException::class, 'Email has not been verified yet', HTTP_FORBIDDEN);
	}
}

(new PostTest())->run();
