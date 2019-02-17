<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Bulletpoint;

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
final class PatchTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		['id' => $userId] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'bulletpoint_ratings', ['user_id' => $userId, 'bulletpoint_id' => $id]))->try();
		$response = (new Endpoint\Bulletpoint\Patch(
			new Application\FakeRequest(new Output\Json([
				'rating' => [
					'user' => 1,
				],
			])),
			$this->connection,
			new Access\FakeUser((string) $userId),
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PatchTest())->run();
