<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Api\Endpoint\Bulletpoint;

use Bulletpoint\Api\Endpoint;
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
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		$response = (new Endpoint\Bulletpoint\Get($this->connection))->response(['id' => $id]);
		$payload = json_decode($response->body()->serialization());
		(new Misc\SchemaAssertion($payload, new \SplFileInfo(Endpoint\Bulletpoint\Get::SCHEMA)))->assert();
		Assert::same(HTTP_OK, $response->status());
	}
}

(new GetTest())->run();
