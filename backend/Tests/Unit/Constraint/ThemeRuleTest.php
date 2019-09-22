<?php
declare(strict_types = 1);

namespace Bulletpoint\Unit\Constraint;

use Bulletpoint\Constraint;
use Bulletpoint\TestCase;
use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
final class ThemeRuleTest extends TestCase\Runtime {
	public function testReplaces(): void {
		Assert::same(
			[
				'reference' => ['url' => 'https://cs.wikipedia.org/wiki/Test'],
				'alternative_names' => [],
				'name' => 'Test',
			],
			(new Constraint\ThemeRule())->apply([
				'reference' => ['url' => 'https://cs.m.wikipedia.org/wiki/Test'],
				'alternative_names' => [],
				'name' => 'Test',
			]),
		);
	}
}

(new ThemeRuleTest())->run();
