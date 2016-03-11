<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class SameRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\FormatException Msg
	*/
	public function testNotSame() {
		(new Constraint\SameRule('Msg', 'abc'))->isSatisfied('cba');
	}

	public function testSame() {
		(new Constraint\SameRule('ok', 'abc'))->isSatisfied('abc');
		Assert::true(true);
	}
}


(new SameRule())->run();
