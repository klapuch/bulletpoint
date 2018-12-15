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
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints', ['content' => 'TEST']))->try();
		$bulletpoint = (new Misc\TestingFormat(
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(
					$id,
					$this->connection
				),
				$id,
				$this->connection
			))->print(new Output\Json())
		))->raw();
		Assert::same($id, $bulletpoint['id']);
		Assert::same('TEST', $bulletpoint['content']);
		Assert::same(['up' => 1, 'down' => 0, 'total' => 1], $bulletpoint['rating']);
	}

	/**
	 * @throws \UnexpectedValueException Bulletpoint 1 does not exist
	 */
	public function testThrowingOnUnknown(): void {
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint(1, $this->connection),
			1,
			$this->connection
		))->print(new Output\Json());
	}
}

(new BulletpointTest())->run();
