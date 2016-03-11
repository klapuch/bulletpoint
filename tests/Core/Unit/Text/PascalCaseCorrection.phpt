<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class PascalCaseCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		mb_internal_encoding('UTF-8');
		$this->correction = new Text\PascalCaseCorrection();
	}

	public function testFormat() {
		Assert::same(
			$this->correction->replacement('WŠŘŽÝ this Is text!123abc'),
			'WšřžýThisIsText!123abc'
		);
	}

	public function testDashFormat() {
		Assert::same(
			$this->correction->replacement('WŠŘŽÝ-this-Is-text!123abc'),
			'WšřžýThisIsText!123abc'
		);
	}
}


(new PascalCaseCorrection())->run();
