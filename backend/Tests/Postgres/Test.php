<?php
declare(strict_types = 1);

namespace Bulletpoint\Postgres;

use Bulletpoint\Misc;
use Bulletpoint\TestCase;
use Klapuch\Storage;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
final class Test extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testPostgres(): void {
		(new class(new \SplFileInfo(__DIR__), $this->connection) implements Misc\Assertion {
			private const PATTERN = '~\.sql$~i';

			private \SplFileInfo $source;

			private Storage\Connection $connection;

			public function __construct(\SplFileInfo $source, Storage\Connection $connection) {
				$this->source = $source;
				$this->connection = $connection;
			}

			public function assert(): void {
				foreach ($this->files($this->source) as $file) {
					$this->import($file);
					$this->test($file);
				}
			}

			private function test(\SplFileInfo $file): void {
				foreach ($this->functions($file) as $function) {
					$this->connection->exec('START TRANSACTION');
					try {
						$this->connection->exec(sprintf('SELECT %s()', $function));
					} catch (\PDOException $e) {
						Assert::fail((new \Bulletpoint\Postgres\PlestException($e, $file))->getMessage());
					} finally {
						$this->connection->exec('ROLLBACK TRANSACTION');
					}
				}
				Assert::true(true);
			}

			private function functions(\SplFileInfo $file): array {
				preg_match_all(
					'~^CREATE FUNCTION (?P<functions>tests\.\w+)\(\)~mi',
					(string) file_get_contents($file->getPathname()),
					$matches,
				);
				return $matches['functions'];
			}

			private function import(\SplFileInfo $file): void {
				try {
					$this->connection->exec((string) file_get_contents($file->getPathname()));
				} catch (\PDOException $e) {
					Assert::fail((new \Bulletpoint\Postgres\PlestException($e, $file))->getMessage());
				}
			}

			private function files(\SplFileInfo $source): \Iterator {
				return new \RegexIterator(
					new \RecursiveIteratorIterator(
						new \RecursiveDirectoryIterator(
							$source->getPathname(),
						),
					),
					self::PATTERN,
				);
			}
		})->assert();
	}
}

(new Test())->run();
