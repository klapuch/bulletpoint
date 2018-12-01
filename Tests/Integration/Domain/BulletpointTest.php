<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class BulletpointTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testBulletpointById(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints', ['text' => 'TEST']))->try();
		$bulletpoint = (new Misc\TestingFormat(
			(new Domain\StoredBulletpoint(
				$id,
				$this->connection
			))->print(new Output\Json())
		))->raw();
		Assert::same($id, $bulletpoint['id']);
		Assert::same('TEST', $bulletpoint['text']);
	}
}

(new BulletpointTest())->run();
