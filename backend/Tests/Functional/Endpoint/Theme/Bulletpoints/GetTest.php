<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Endpoint\Theme\Bulletpoints;

use Bulletpoint\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

/**
 * @testCase
 */
final class GetTest extends TestCase\Runtime {
	use TestCase\Page;

	public function testSuccessfulResponse(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['theme_id' => $id]))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['theme_id' => $id]))->try();
		$response = (new Endpoint\Theme\Bulletpoints\Get($this->connection))->response(['theme_id' => $id]);
		$payload = json_decode($response->body()->serialization());
		(new Misc\SchemaAssertion($payload, new \SplFileInfo(Endpoint\Theme\Bulletpoints\Get::SCHEMA)))->assert();
		Assert::same(HTTP_OK, $response->status());
	}
}

(new GetTest())->run();
