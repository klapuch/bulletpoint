<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Storage\TypedQuery;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class RatingTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testAddingRating(): void {
		['id' => $bulletpoint] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(+1);
		Assert::same(2, (new TypedQuery($this->connection, 'SELECT sum(point) FROM bulletpoint_ratings'))->field());
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(0);
		Assert::same(1, (new TypedQuery($this->connection, 'SELECT sum(point) FROM bulletpoint_ratings'))->field());
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(-1);
		Assert::same(0, (new TypedQuery($this->connection, 'SELECT sum(point) FROM bulletpoint_ratings'))->field());
	}

	public function testOnePerUser(): void {
		['id' => $bulletpoint] = (new Fixtures\SamplePostgresData($this->connection, 'bulletpoints'))->try();
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		Assert::same(1, (new TypedQuery($this->connection, 'SELECT sum(point) FROM bulletpoint_ratings'))->field());
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(+1);
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(+1);
		(new Domain\BulletpointRating($bulletpoint, new Access\FakeUser((string) $user), $this->connection))->rate(+1);
		Assert::same(2, (new TypedQuery($this->connection, 'SELECT sum(point) FROM bulletpoint_ratings'))->field());
	}
}

(new RatingTest())->run();
