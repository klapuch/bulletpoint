<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\ContributedBulletpoint;

use Bulletpoint\Domain\Access;
use Bulletpoint\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Application;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class PutTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		['id' => $userId] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'contributed_bulletpoints', ['user_id' => $userId]))->try();
		$response = (new Endpoint\ContributedBulletpoint\Put(
			new Application\FakeRequest(new Output\Json([
				'content' => 'TEST OK!',
				'referenced_theme_id' => null,
				'source' => [
					'link' => 'https://www.wikipedia.com',
					'type' => 'web',
				],
			])),
			$this->connection,
			new Access\FakeUser((string) $userId)
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PutTest())->run();
