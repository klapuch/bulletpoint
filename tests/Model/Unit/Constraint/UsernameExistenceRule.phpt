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

final class UsernameExistenceRule extends \Tester\TestCase {
	/**
	* @throws \Bulletpoint\Exception\ExistenceException Přezdívka neexistuje
	*/
	public function testUnknownUsername() {
		(new Constraint\UsernameExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied('face');
	}

	public function testExistingUsername() {
		(new Constraint\UsernameExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied('facedown');
		Assert::true(true);
	}
}


(new UsernameExistenceRule())->run();
