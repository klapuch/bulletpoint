<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
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
		(new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['content' => 'TEST']))->try();
		$bulletpoint = (new Misc\TestingFormat(
			(new Domain\PublicBulletpoint(
				new Domain\ExistingBulletpoint(
					new Domain\StoredBulletpoint(
						$id,
						$this->connection,
						new Access\FakeUser(),
					),
					$id,
					$this->connection,
				),
			))->print(new Output\Json()),
		))->raw();
		Assert::same($id, $bulletpoint['id']);
		Assert::same('TEST', $bulletpoint['content']);
		Assert::same(['up' => 1, 'down' => 0, 'total' => 1, 'user' => 0], $bulletpoint['rating']);
	}

	public function testEditing(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($id, $this->connection, new Access\FakeUser()),
			$id,
			$this->connection,
		))->edit([
			'source' => [
				'link' => 'https://www.wikipedia.com',
				'type' => 'web',
			],
			'content' => 'TEST OK!',
			'referenced_theme_id' => [],
			'compared_theme_id' => [],
		]);
		$bulletpoint = (new Storage\TypedQuery($this->connection, 'SELECT * FROM web.bulletpoints WHERE id = ?', [$id]))->row();
		Assert::same($id, $bulletpoint['id']);
		Assert::same('TEST OK!', $bulletpoint['content']);
	}

	public function testDeleting(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		(new Domain\ExistingBulletpoint(
			new Domain\StoredBulletpoint($id, $this->connection, new Access\FakeUser()),
			$id,
			$this->connection,
		))->delete();
		Assert::same(0, (new Storage\TypedQuery($this->connection, 'SELECT count(*) FROM public_bulletpoints'))->field());
	}

	public function testThrowingOnUnknown(): void {
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection, new Access\FakeUser()),
				1,
				$this->connection,
			))->print(new Output\Json());
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection, new Access\FakeUser()),
				1,
				$this->connection,
			))->edit([]);
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection, new Access\FakeUser()),
				1,
				$this->connection,
			))->delete();
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
		Assert::exception(function() {
			(new Domain\ExistingBulletpoint(
				new Domain\StoredBulletpoint(1, $this->connection, new Access\FakeUser()),
				1,
				$this->connection,
			))->rate(1);
		}, \UnexpectedValueException::class, 'Bulletpoint 1 does not exist');
	}
}

(new BulletpointTest())->run();
