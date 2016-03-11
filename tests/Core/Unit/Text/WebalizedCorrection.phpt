<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class WebalizedCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		$this->correction = new Text\WebalizedCorrection();
	}

	public function testFormat() {
		Assert::same(
			$this->correction->replacement('_Hi,how1 are_you-?I\'m f%ne'),
			'_hi-how1-are_you-i-m-f-ne'
		);
		Assert::same($this->correction->replacement('"666"'), '666');
	}
}


(new WebalizedCorrection())->run();
