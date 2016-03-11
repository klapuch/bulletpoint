<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class UsernameNotExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Přezdívka již existuje
	*/
	public function testUnknownEmail() {
		(new Constraint\UsernameNotExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied('face');
	}

	public function testExistingEmail() {
		(new Constraint\UsernameNotExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied('facedown');
		Assert::true(true);
	}
}


(new UsernameNotExistenceRule())->run();
