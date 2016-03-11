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

final class EmailNotExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Email foo@bar.cz již existuje
	*/
	public function testUnknownEmail() {
		(new Constraint\EmailNotExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied('foo@bar.cz');
	}

	public function testExistingEmail() {
		(new Constraint\EmailNotExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied('facedown@facedown.cz');
		Assert::true(true);
	}
}


(new EmailNotExistenceRule())->run();
