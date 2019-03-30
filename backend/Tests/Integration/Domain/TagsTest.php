<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain;

use Bulletpoint\Domain;
use Bulletpoint\TestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class TagsTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase;

	public function testAdding(): void {
		$tags = new Domain\StoredTags($this->connection);
		$tags->add('ABC');
		$tags->add('DEF');
		Assert::same([['name' => 'ABC', 'id' => 1], ['name' => 'DEF', 'id' => 2]], $tags->all());
	}

	public function testThrowingOnDuplicity(): void {
		$tags = new Domain\UniqueTags(new Domain\StoredTags($this->connection), $this->connection);
		$tags->add('ABC');
		Assert::exception(static function () use ($tags): void {
			$tags->add('ABC');
		}, \UnexpectedValueException::class, 'Tag "ABC" already exists.');
	}
}

(new TagsTest())->run();
