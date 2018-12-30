<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Output;
use Klapuch\Storage;
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
		Assert::same(['up' => 1, 'down' => 0, 'total' => 1, 'user' => 0], $bulletpoint['rating']);
	}

	public function testEditing(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($id, $this->connection),
			$id,
			$this->connection
		))->edit([
			'source' => [
				'link' => 'https://www.wikipedia.com',
				'type' => 'web',
			],
			'content' => 'TEST OK!',
		]);
		$bulletpoint = (new Storage\TypedQuery($this->connection, 'SELECT * FROM public_bulletpoints WHERE id = ?', [$id]))->row();
		Assert::same($id, $bulletpoint['id']);
		Assert::same('TEST OK!', $bulletpoint['content']);
	}

	public function testDeleting(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($id, $this->connection),
			$id,
			$this->connection
		))->delete();
		Assert::same(0, (new Storage\TypedQuery($this->connection, 'SELECT count(*) FROM bulletpoints'))->field());
	}

	public function testThrowingOnUnknown(): void {
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection),
				1,
				$this->connection
			))->print(new Output\Json());
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection),
				1,
				$this->connection
			))->edit([]);
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection),
				1,
				$this->connection
			))->delete();
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
	}
}

(new BulletpointTest())->run();
