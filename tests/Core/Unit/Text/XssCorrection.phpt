<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class XssCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		$this->correction = new Text\XssCorrection();
	}

	public function testCorrectedXss() {
		Assert::same(
			$this->correction->replacement('<&>"\'a,b,c!?123'),
			'&lt;&amp;&gt;&quot;&#039;a,b,c!?123'
		);
	}
}


(new XssCorrection())->run();
