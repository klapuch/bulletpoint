<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class LowerCaseCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		mb_internal_encoding('UTF-8');
		$this->correction = new Text\LowerCaseCorrection();
	}

	public function testFormat() {
		Assert::same(
			$this->correction->replacement('ĚŠČřŽýáÍéabcd!123'),
			'ěščřžýáíéabcd!123'
		);
	}
}


(new LowerCaseCorrection())->run();
