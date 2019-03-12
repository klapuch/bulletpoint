<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access;
use Bulletpoint\TestCase;
use Klapuch\Http;
use Klapuch\Storage;
use Nette\Utils\Json;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class OAuthEntranceTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testInsertingNewFacebookAccount(): void {
		Assert::equal(
			new Access\ConstantUser(
				'1',
				[
					'email' => 'new@bar.cz',
					'role' => 'member',
					'id' => 1,
					'username' => null,
					'password' => null,
					'facebook_id' => 123,
				],
			),
			(new Access\OAuthEntrance(
				$this->connection,
				new Http\FakeRequest(
					new Http\FakeResponse(
						Json::encode(['id' => 123, 'email' => 'new@bar.cz']),
					),
				),
			))->enter(['login' => 'TOKEN']),
		);
	}

	public function testUpdatingEmailOfAlreadyRegistered(): void {
		[$facebookId, $email] = [123, 'old@bar.cz'];
		(new Storage\NativeQuery($this->connection, 'INSERT INTO users (facebook_id, email) VALUES (?, ?)', [$facebookId, $email]))->execute();
		Assert::equal(
			new Access\ConstantUser(
				'1',
				[
					'email' => 'new@bar.cz',
					'role' => 'member',
					'id' => 1,
					'username' => null,
					'password' => null,
					'facebook_id' => 123,
				],
			),
			(new Access\OAuthEntrance(
				$this->connection,
				new Http\FakeRequest(
					new Http\FakeResponse(
						Json::encode(['id' => $facebookId, 'email' => 'new@bar.cz']),
					),
				),
			))->enter(['login' => 'TOKEN']),
		);
	}

	public function testMergingGoogleWithFacebook(): void {
		Environment::skip('TODO!!');
		[$facebookId, $email] = [123, 'old@bar.cz'];
		(new Storage\NativeQuery($this->connection, 'INSERT INTO users (facebook_id, email) VALUES (?, ?)', [$facebookId, $email]))->execute();
		Assert::equal(
			new Access\ConstantUser(
				'1',
				[
					'email' => 'new@bar.cz',
					'role' => 'member',
					'id' => 1,
					'username' => null,
					'password' => null,
					'facebook_id' => 123,
				],
			),
			(new Access\OAuthEntrance(
				$this->connection,
				new Http\FakeRequest(
					new Http\FakeResponse(
						Json::encode(['id' => $facebookId, 'email' => 'new@bar.cz']),
					),
				),
			))->enter(['login' => 'TOKEN']),
		);
	}
}

(new OAuthEntranceTest())->run();
