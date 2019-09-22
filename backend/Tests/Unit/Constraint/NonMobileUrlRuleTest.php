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
final class NonMobileUrlRuleTest extends TestCase\Runtime {
	/**
	 * @dataProvider replaces
	 */
	public function testReplaces(string $from, string $to): void {
		Assert::same($to, (new Constraint\NonMobileUrlRule())->apply($from));
	}

	protected function replaces(): \Generator {
		yield ['https://cs.m.wikipedia.org/wiki/Test', 'https://cs.wikipedia.org/wiki/Test'];
		yield ['https://cs.wikipedia.org/wiki/Test', 'https://cs.wikipedia.org/wiki/Test'];
	}
}

(new NonMobileUrlRuleTest())->run();
