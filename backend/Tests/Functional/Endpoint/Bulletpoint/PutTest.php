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
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		$response = (new Endpoint\Bulletpoint\Put(
			new Application\FakeRequest(new Output\Json([
				'content' => 'TEST OK!',
				'referenced_theme_id' => [],
				'compared_theme_id' => [],
				'source' => [
					'link' => 'https://www.wikipedia.com',
					'type' => 'web',
				],
			])),
			$this->connection,
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}

	public function testAllowingNullForHeadType(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		$response = (new Endpoint\Bulletpoint\Put(
			new Application\FakeRequest(new Output\Json([
				'content' => 'TEST OK!',
				'referenced_theme_id' => [],
				'compared_theme_id' => [],
				'source' => [
					'link' => null,
					'type' => 'head',
				],
			])),
			$this->connection,
		))->response(['id' => $id]);
		Assert::same(HTTP_NO_CONTENT, $response->status());
	}

	public function testForbiddingNullForWebType(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		Assert::exception(function () use ($id): void {
			(new Endpoint\Bulletpoint\Put(
				new Application\FakeRequest(new Output\Json([
					'content' => 'TEST OK!',
					'referenced_theme_id' => [],
					'compared_theme_id' => [],
					'source' => [
						'link' => null,
						'type' => 'web',
					],
				])),
				$this->connection,
			))->response(['id' => $id]);
		}, \UnexpectedValueException::class, 'URL of source is not valid');
	}
}

(new PutTest())->run();
