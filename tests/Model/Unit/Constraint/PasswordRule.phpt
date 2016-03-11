<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class PasswordRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\FormatException Heslo musí mít aspoň 6 znaků
	*/
	public function testEmptyPassword() {
		(new Constraint\PasswordRule)->isSatisfied('');
	}

	/**
	* @throws Bulletpoint\Exception\FormatException Heslo musí mít aspoň 6 znaků
	*/
	public function testShortPassword() {
		(new Constraint\PasswordRule)->isSatisfied('#^čřž'); //also checks mb_
	}

	public function testValidPassword() {
		(new Constraint\PasswordRule)->isSatisfied('12345°°˛``~~##łłłłŁŁkkk');
		(new Constraint\PasswordRule)->isSatisfied('123456');
		Assert::true(true);
	}
}


(new PasswordRule())->run();
