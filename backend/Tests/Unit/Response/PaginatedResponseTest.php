<?php
declare(strict_types = 1);

namespace Bulletpoint\Unit\Response;

use Bulletpoint\Response;
use Bulletpoint\TestCase;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\UI;
use Klapuch\Uri;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class PaginatedResponseTest extends TestCase\Runtime {
	public function testPriorityToNewHeader(): void {
		Assert::notSame(
			['Link' => 'xxx'],
			(new Response\PaginatedResponse(
				new Application\FakeResponse(null, ['Link' => 'xxx']),
				10,
				new UI\FakePagination([]),
				new Uri\FakeUri(),
			))->headers(),
		);
	}

	public function testAddingOtherHeaders(): void {
		Assert::contains(
			'text/html',
			(new Response\PaginatedResponse(
				new Application\FakeResponse(null, ['Accept' => 'text/html']),
				10,
				new UI\FakePagination([]),
				new Uri\FakeUri(),
			))->headers(),
		);
	}

	public function testPartialResponseForNotLastPage(): void {
		Assert::same(
			HTTP_PARTIAL_CONTENT,
			(new Response\PaginatedResponse(
				new class implements Application\Response {
					public function body(): Output\Format {
					}

					public function headers(): array {
						return [];
					}

					public function status(): int {
						return HTTP_MOVED_PERMANENTLY;
					}
				},
				5,
				new UI\FakePagination([1, 9]),
				new Uri\FakeUri(),
			))->status(),
		);
	}

	public function testDelegatedStatusCodeForLastPage(): void {
		Assert::same(
			HTTP_CREATED,
			(new Response\PaginatedResponse(
				new class implements Application\Response {
					public function body(): Output\Format {
					}

					public function headers(): array {
						return [];
					}

					public function status(): int {
						return HTTP_CREATED;
					}
				},
				10,
				new UI\FakePagination([1, 10]),
				new Uri\FakeUri(),
			))->status(),
		);
	}

	public function testDelegatedStatusCodeForOversteppingLastPage(): void {
		Assert::same(
			HTTP_NO_CONTENT,
			(new Response\PaginatedResponse(
				new class implements Application\Response {
					public function body(): Output\Format {
					}

					public function headers(): array {
						return [];
					}

					public function status(): int {
						return HTTP_NO_CONTENT;
					}
				},
				20,
				new UI\FakePagination([1, 10]),
				new Uri\FakeUri(),
			))->status(),
		);
	}
}

(new PaginatedResponseTest())->run();
