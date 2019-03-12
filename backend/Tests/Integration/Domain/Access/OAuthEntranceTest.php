<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Access;

use Bulletpoint\Domain\Access;
use Bulletpoint\TestCase;
use Klapuch\Http;
use Nette\Utils\Json;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class OAuthEntranceTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testCreatingNewUser(): void {
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
					'google_id' => null,
				],
			),
			(new Access\OAuthEntrance(
				'facebook',
				$this->connection,
				new Http\FakeRequest(
					new Http\FakeResponse(
						Json::encode(['id' => 123, 'email' => 'new@bar.cz']),
					),
				),
			))->enter(['login' => 'TOKEN']),
		);
	}
}

(new OAuthEntranceTest())->run();
