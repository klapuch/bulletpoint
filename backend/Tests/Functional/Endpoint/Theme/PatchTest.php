<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Theme;

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

	public function testStarring(): void {
		['id' => $userId] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		$response = (new Endpoint\Theme\Patch(
			new Application\FakeRequest(new Output\Json(['is_starred' => true])),
			$this->connection,
			new Access\FakeUser((string) $userId),
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}

	public function testUnstarring(): void {
		['id' => $userId] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		$response = (new Endpoint\Theme\Patch(
			new Application\FakeRequest(new Output\Json(['is_starred' => false])),
			$this->connection,
			new Access\FakeUser((string) $userId),
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PatchTest())->run();
