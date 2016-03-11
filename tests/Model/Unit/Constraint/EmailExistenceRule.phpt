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

final class EmailExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Email foo@bar.cz neexistuje
	*/
	public function testUnknownEmail() {
		(new Constraint\EmailExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied('foo@bar.cz');
	}

	public function testExistingEmail() {
		(new Constraint\EmailExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied('facedown@facedown.cz');
		Assert::true(true);
	}
}


(new EmailExistenceRule())->run();
