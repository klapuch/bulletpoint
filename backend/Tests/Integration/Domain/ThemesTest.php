<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Dataset;
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
		(new Domain\StoredThemes(new Access\FakeUser($user), $this->connection))->create([
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
		$themes = (new Domain\PublicThemes(
			new Domain\StoredThemes(
				new Access\FakeUser(),
				$this->connection
			)
		));
		Assert::count(3, iterator_to_array($themes->all(new Dataset\EmptySelection())));
		Assert::same(3, $themes->count(new Dataset\EmptySelection()));
	}
}

(new ThemesTest())->run();
