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

final class DocumentSlugExistenceRule extends \Tester\TestCase {
	/**
	* @throws Bulletpoint\Exception\ExistenceException Slug neexistuje
	*/
	public function testUnknownEmail() {
		(new Constraint\DocumentSlugExistenceRule(
			new Fake\Database($fetch = false)
		))->isSatisfied('sl-ug');
	}

	public function testExistingEmail() {
		(new Constraint\DocumentSlugExistenceRule(
			new Fake\Database($fetch = true)
		))->isSatisfied(2);
		Assert::true(true);
	}
}


(new DocumentSlugExistenceRule())->run();
