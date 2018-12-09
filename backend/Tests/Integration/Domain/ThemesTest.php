<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Fixtures;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;

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
		(new Domain\StoredThemes(new Domain\FakeUser($user), $this->connection))->create([
			'name' => 'TEST',
			'tags' => [$tag1, $tag2],
			'reference' => [
				'url' => 'https://www.wikipedia.cz/test',
			],
		]);
		(new Misc\TableCount($this->connection, 'themes', 1))->assert();
		(new Misc\TableCount($this->connection, 'references', 1))->assert();
	}
}

(new ThemesTest())->run();
