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

final class DocumentProposalExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Návrh neexistuje
	*/
	public function testUnknownEmail() {
		(new Constraint\DocumentProposalExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied(1);
	}

	public function testExistingEmail() {
		(new Constraint\DocumentProposalExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied(2);
		Assert::true(true);
	}
}


(new DocumentProposalExistenceRule())->run();
