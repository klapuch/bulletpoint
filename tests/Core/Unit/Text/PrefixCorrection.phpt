<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class PrefixCorrection extends \Tester\TestCase {
	private $correction;
	const PREFIX = 'Face';

	protected function setUp() {
		$this->correction = new Text\PrefixCorrection(self::PREFIX);
	}

	public function testPrefix() {
		Assert::same(
			self::PREFIX . 'Down',
			$this->correction->replacement('Down')
		);
	}
}


(new PrefixCorrection())->run();
