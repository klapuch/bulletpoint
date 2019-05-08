<?php
declare(strict_types = 1);

namespace Bulletpoint\Functional\Api\Endpoint\Themes;

use Bulletpoint\Api\Endpoint;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Uri\FakeUri;
use Tester\Assert;

require __DIR__ . '/../../../../bootstrap.php';

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

	public function testSingleTag(): void {
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $theme1] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $theme2] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag1, 'theme_id' => $theme1]))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag2, 'theme_id' => $theme2]))->try();
		$response = (new Endpoint\Themes\Get(
			$this->connection,
			new FakeUri(),
		))->response(['tag_id' => (string) $tag1, 'sort' => '', 'page' => 1, 'per_page' => 1]);
		Assert::count(1, json_decode($response->body()->serialization()));
		Assert::same(HTTP_OK, $response->status());
	}

	public function testMultipleTags(): void {
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $theme1] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $theme2] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag1, 'theme_id' => $theme1]))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag2, 'theme_id' => $theme2]))->try();
		$response = (new Endpoint\Themes\Get(
			$this->connection,
			new FakeUri(),
		))->response(['tag_id' => [$tag1, $tag2], 'sort' => '', 'page' => 1, 'per_page' => 2]);
		Assert::count(2, json_decode($response->body()->serialization()));
		Assert::same(HTTP_OK, $response->status());
	}
}

(new GetTest())->run();
