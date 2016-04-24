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

final class InformationSourceExistenceRule extends \Tester\TestCase {
	/**
	* @throws \Bulletpoint\Exception\ExistenceException Zdroj neexistuje
	*/
	public function testUnknownSource() {
		(new Constraint\InformationSourceExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied(1);
	}

	public function testExistingSource() {
		(new Constraint\InformationSourceExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied(2);
		Assert::true(true);
	}
}


(new InformationSourceExistenceRule())->run();
