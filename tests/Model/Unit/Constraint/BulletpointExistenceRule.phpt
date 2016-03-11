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

final class BulletpointExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Bulletpoint neexistuje
	*/
	public function testUnknownEmail() {
		(new Constraint\BulletpointExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied(1);
	}

	public function testExistingEmail() {
		(new Constraint\BulletpointExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied(2);
		Assert::true(true);
	}
}


(new BulletpointExistenceRule())->run();
