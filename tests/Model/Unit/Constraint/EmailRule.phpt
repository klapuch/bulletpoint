<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class EmailRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\FormatException Email je neplatný
	*/
	public function testEmptyEmail() {
		(new Constraint\EmailRule)->isSatisfied('');
	}

	public function testValidEmail() {
		(new Constraint\EmailRule)->isSatisfied('facedown@facedown.cz');
		Assert::true(true);
	}
}


(new EmailRule())->run();
