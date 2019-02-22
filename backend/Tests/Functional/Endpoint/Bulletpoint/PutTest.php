<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Bulletpoint;

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
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		$response = (new Endpoint\Bulletpoint\Put(
			new Application\FakeRequest(new Output\Json([
				'content' => 'TEST OK!',
				'referenced_theme_id' => null,
				'source' => [
					'link' => 'https://www.wikipedia.com',
					'type' => 'web',
				],
			])),
			$this->connection,
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}
}

(new PutTest())->run();
