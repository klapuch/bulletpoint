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
final class ThemeTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testThemeById(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes', ['name' => 'TEST']))->try();
		$theme = (new Misc\TestingFormat(
			(new Domain\ExistingTheme(
				new Domain\StoredTheme($id, $this->connection),
				$id,
				$this->connection
			))->print(new Output\Json())
		))->raw();
		Assert::same($id, $theme['id']);
		Assert::same('TEST', $theme['name']);
	}

	public function testChanging(): void {
		['id' => $id] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag3] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag1, 'theme_id' => $id]))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag2, 'theme_id' => $id]))->try();
		(new Domain\ExistingTheme(
			new Domain\StoredTheme($id, $this->connection),
			$id,
			$this->connection
		))->change([
			'name' => 'TEST OK!',
			'tags' => [$tag1, $tag3],
			'reference' => [
				'url' => 'https://www.wikipedia.cz/test',
			],
		]);
		$theme = (new Storage\TypedQuery($this->connection, 'SELECT * FROM web.themes WHERE id = ?', [$id]))->row();
		Assert::same('TEST OK!', $theme['name']);
		Assert::same([1, 3], array_column($theme['tags'], 'id'));
	}

	public function testThrowingOnUnknown(): void {
		Assert::exception(function () {
			(new Domain\ExistingTheme(
				new Domain\StoredTheme(1, $this->connection),
				1,
				$this->connection
			))->print(new Output\Json());
		}, \UnexpectedValueException::class, 'Theme 1 does not exist');
		Assert::exception(function () {
			(new Domain\ExistingTheme(
				new Domain\StoredTheme(1, $this->connection),
				1,
				$this->connection
			))->change([]);
		}, \UnexpectedValueException::class, 'Theme 1 does not exist');
	}
}

(new ThemeTest())->run();
