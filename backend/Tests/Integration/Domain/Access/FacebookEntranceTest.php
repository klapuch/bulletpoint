<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access\ConstantUser;
use Bulletpoint\Domain\Access\FacebookEntrance;
use Bulletpoint\Http;
use Bulletpoint\TestCase;
use GuzzleHttp\Client;
use Klapuch\Storage\Connection;
use Klapuch\Storage\NativeQuery;
use Klapuch\Storage\TypedQuery;
use Klapuch\Uri;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class FacebookEntranceTest extends TestCase\Runtime {
	use TestCase\Mockery, TestCase\TemplateDatabase {
		TestCase\Mockery::tearDown as mockeryTeardown;
		TestCase\TemplateDatabase::tearDown as databaseTeardow;
	}

	/**
	 * @throws \UnexpectedValueException Error during retrieving Facebook token.
	 */
	public function testThrowingOnNotOkStatus(): void {
		$http = $this->mock(Client::class);
		$response = $this->mock(ResponseInterface::class);
		$response->shouldReceive('getStatusCode')->andReturn(HTTP_BAD_REQUEST);
		$response->shouldReceive('getBody')->andReturn('');
		$http->shouldReceive('request')->andReturn($response);
		(new FacebookEntrance($this->connection, $http))->enter(['login' => 'abc']);
	}

	public function testInsertingNewFacebookAccount(): void {
		$http = $this->mock(Client::class);
		$response = $this->mock(ResponseInterface::class);
		$response->shouldReceive('getStatusCode')->andReturn(HTTP_OK);
		$response->shouldReceive('getBody')->andReturn(Json::encode(['id' => 123, 'email' => 'new@bar.cz']));
		$http->shouldReceive('request')->andReturn($response);
		Assert::equal(
			new ConstantUser(
				'1',
				[
					'email' => 'new@bar.cz',
					'role' => 'member',
					'id' => 1,
					'username' => null,
					'password' => null,
					'facebook_id' => 123,
				]
			),
			(new FacebookEntrance($this->connection, $http))->enter(['login' => 'TOKEN'])
		);
	}

	public function testUpdatingEmailOfAlreadyRegistered(): void {
		[$facebookId, $email] = [123, 'old@bar.cz'];
		(new NativeQuery($this->connection, 'INSERT INTO users (facebook_id, email) VALUES (?, ?)', [$facebookId, $email]))->execute();
		$http = $this->mock(Client::class);
		$response = $this->mock(ResponseInterface::class);
		$response->shouldReceive('getStatusCode')->andReturn(HTTP_OK);
		$response->shouldReceive('getBody')->andReturn(Json::encode(['id' => $facebookId, 'email' => 'new@bar.cz']));
		$http->shouldReceive('request')->andReturn($response);
		Assert::equal(
			new ConstantUser(
				'1',
				[
					'email' => 'new@bar.cz',
					'role' => 'member',
					'id' => 1,
					'username' => null,
					'password' => null,
					'facebook_id' => 123,
				]
			),
			(new FacebookEntrance($this->connection, $http))->enter(['login' => 'TOKEN'])
		);
	}

	protected function tearDown(): void {
		$this->mockeryTeardown();
		$this->databaseTeardow();
	}
}

(new FacebookEntranceTest())->run();
