<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\Misc;
use Bulletpoint\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ThemesTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testCreatingNewTheme(): void {
		(new Domain\StoredThemes($this->connection))->create([
			'name' => 'TEST',
			'tags' => [1, 2],
			'reference' => [
				'name' => 'wikipedia',
				'url' => 'https://www.wikipedia.cz/test',
			],
		]);
		(new Misc\TableCount($this->connection, 'themes', 1))->assert();
		(new Misc\TableCount($this->connection, 'references', 1))->assert();
	}
}

(new ThemesTest())->run();
