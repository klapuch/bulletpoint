<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Themes;

use Bulletpoint\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Uri\FakeUri;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class GetTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['theme_id' => $id]))->try();
		$response = (new Endpoint\Themes\Get(
			$this->connection,
			new FakeUri(),
		))->response(['id' => $id, 'sort' => '', 'page' => 1, 'per_page' => 1]);
		$payload = json_decode($response->body()->serialization());
		(new Misc\SchemaAssertion($payload, new \SplFileInfo(Endpoint\Theme\Get::SCHEMA)))->assert();
		Assert::same(HTTP_OK, $response->status());
	}
}

(new GetTest())->run();
