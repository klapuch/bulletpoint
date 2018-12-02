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

	/**
	 * @throws \UnexpectedValueException Theme 1 does not exist
	 */
	public function testThrowingOnUnknown(): void {
		(new Domain\ExistingTheme(
			new Domain\StoredTheme(1, $this->connection),
			1,
			$this->connection
		))->print(new Output\Json());
	}
}

(new ThemeTest())->run();
