<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Theme\Bulletpoints;

use Bulletpoint\Domain\Access;
use Bulletpoint\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Application;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class PostTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $referencedThemeId] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $userId] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		$response = (new Endpoint\Theme\Bulletpoints\Post(
			new Application\FakeRequest(new Output\Json([
				'content' => 'This is [[referenced]]',
				'referenced_theme_id' => [$referencedThemeId],
				'source' => [
					'link' => 'https://www.wikipedia.com',
					'type' => 'web',
				],
			])),
			$this->connection,
			new Access\FakeUser((string) $userId),
		))->response(['theme_id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PostTest())->run();
