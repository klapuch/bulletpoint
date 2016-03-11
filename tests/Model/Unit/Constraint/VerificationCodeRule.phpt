<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class VerificationCodeRule extends \Tester\TestCase {
	protected function invalidCodes() {
		return [
			[0],
			['FoooBar'],
		];
	}

	/**
	* @dataProvider invalidCodes
	*/
	public function testInvalidCodes($code) {
		Assert::exception(function() use ($code) {
			(new Constraint\VerificationCodeRule(new Fake\Database))->isSatisfied($code);
		}, 'Bulletpoint\Exception\FormatException', 'Ověřovací kód nemá správný formát');
	}

	public function testExistingCode() {
		(new Constraint\VerificationCodeRule(new Fake\Database($fetch = true)))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
		Assert::true(true);
	}

	/**
	* @throws Bulletpoint\Exception\ExistenceException Ověřovací kód neexistuje
	*/
	public function testUnknownCode() {
		(new Constraint\VerificationCodeRule(new Fake\Database($fetch = false)))
		->isSatisfied('e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363');
	}
}


(new VerificationCodeRule())->run();
