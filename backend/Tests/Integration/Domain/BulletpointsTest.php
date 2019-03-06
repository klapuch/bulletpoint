<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class BulletpointsTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testAllByTheme(): void {
		['id' => $theme] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints'))->try();
		['id' => $id1] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['theme_id' => $theme]))->try();
		['id' => $id2] = (new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['theme_id' => $theme]))->try();
		$bulletpoints = array_map(
			static function (Domain\Bulletpoint $bulletpoint): array {
				return (new Misc\TestingFormat($bulletpoint->print(new Output\Json())))->raw();
			},
			iterator_to_array((new Domain\ThemeBulletpoints($theme, $this->connection, new Access\FakeUser()))->all()),
		);
		Assert::count(2, $bulletpoints);
		Assert::same($id2, $bulletpoints[0]['id']);
		Assert::same($id1, $bulletpoints[1]['id']);
		Assert::same(['up' => 1, 'down' => 0, 'total' => 1, 'user' => 0], $bulletpoints[0]['rating']);
		Assert::same(['up' => 1, 'down' => 0, 'total' => 1, 'user' => 0], $bulletpoints[1]['rating']);
	}

	public function testAddingNew(): void {
		['id' => $theme] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		(new Domain\ThemeBulletpoints($theme, $this->connection, new Access\FakeUser((string) $user)))->add([
			'content' => 'TEST',
			'source' => [
				'link' => 'https://www.wikipedia.cz/test',
				'type' => 'web',
			],
			'referenced_theme_id' => [],
			'compared_theme_id' => [],
		]);
		(new Misc\TableCount($this->connection, 'public_bulletpoints', 1))->assert();
		(new Misc\TableCount($this->connection, 'sources', 1))->assert();
	}

	public function testCountingByTheme(): void {
		['id' => $theme] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		Assert::same(0, (new Domain\ThemeBulletpoints($theme, $this->connection, new Access\FakeUser()))->count());
		(new Fixtures\SamplePostgresData($this->connection, 'public_bulletpoints', ['theme_id' => $theme]))->try();
		Assert::same(1, (new Domain\ThemeBulletpoints($theme, $this->connection, new Access\FakeUser()))->count());
	}
}

(new BulletpointsTest())->run();
