<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class UsernameRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\FormatException Přezdívka se smí skládat z písmen nebo číslic od 3 do 30 znaků
	*/
	public function testEmptyUsername() {
		(new Constraint\UsernameRule)->isSatisfied('');
	}

	/**
	* @throws Bulletpoint\Exception\FormatException Přezdívka se smí skládat z písmen nebo číslic od 3 do 30 znaků
	*/
	public function testInvalidUsername() {
		(new Constraint\UsernameRule)->isSatisfied('ab                     c');
	}

	public function testValidUsername() {
		(new Constraint\UsernameRule)->isSatisfied('Fac3Down2');
		(new Constraint\UsernameRule)->isSatisfied('facedown');
		Assert::true(true);
	}
}


(new UsernameRule())->run();
