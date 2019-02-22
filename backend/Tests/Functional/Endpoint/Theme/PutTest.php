<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Theme;

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
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		$response = (new Endpoint\Theme\Put(
			new Application\FakeRequest(new Output\Json([
				'name' => 'TEST OK!',
				'tags' => [$tag1, $tag2],
				'alternative_names' => ['ABC'],
				'reference' => [
					'url' => 'https://www.wikipedia.com',
				],
			])),
			$this->connection,
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PutTest())->run();
