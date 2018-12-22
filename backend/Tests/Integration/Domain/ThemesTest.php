<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Dataset;
use Klapuch\Output;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ThemesTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testCreatingNewTheme(): void {
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		(new Domain\StoredThemes(new Access\FakeUser((string) $user), $this->connection))->create([
			'name' => 'TEST',
			'tags' => [$tag1, $tag2],
			'reference' => [
				'url' => 'https://www.wikipedia.cz/test',
			],
		]);
		(new Misc\TableCount($this->connection, 'themes', 1))->assert();
		(new Misc\TableCount($this->connection, 'references', 1))->assert();
	}

	public function testGivingAllThemes(): void {
		(new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		$themes = new Domain\PublicThemes(new Domain\StoredThemes(new Access\FakeUser(), $this->connection));
		Assert::count(3, iterator_to_array($themes->all(new Dataset\EmptySelection())));
		Assert::same(3, $themes->count(new Dataset\EmptySelection()));
	}

	public function testGivingByTag(): void {
		['id' => $tag1] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $tag2] = (new Fixtures\SamplePostgresData($this->connection, 'tags'))->try();
		['id' => $theme1] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		['id' => $theme2] = (new Fixtures\SamplePostgresData($this->connection, 'themes'))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag1, 'theme_id' => $theme1]))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'theme_tags', ['tag_id' => $tag2, 'theme_id' => $theme2]))->try();
		$themes = new Domain\TaggedThemes(new Domain\FakeThemes(), $tag1, $this->connection);
		Assert::count(1, iterator_to_array($themes->all(new Dataset\EmptySelection())));
		Assert::same(1, $themes->count(new Dataset\EmptySelection()));
	}

	public function testSearchingByName(): void {
		['id' => $theme1] = (new Fixtures\SamplePostgresData($this->connection, 'themes', ['name' => 'php']))->try();
		(new Fixtures\SamplePostgresData($this->connection, 'themes', ['super cool thing']))->try();
		$themes = new Domain\SearchedThemes(new Domain\FakeThemes(), 'PH', $this->connection);
		$all = iterator_to_array($themes->all(new Dataset\EmptySelection()));
		Assert::count(1, $all);
		Assert::same(1, $themes->count(new Dataset\EmptySelection()));
		Assert::same($theme1, (new Misc\TestingFormat($all[0]->print(new Output\Json())))->raw()['id']);
	}
}

(new ThemesTest())->run();
