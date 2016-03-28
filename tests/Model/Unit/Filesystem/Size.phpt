<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Model\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class Size extends \Tester\TestCase {
	public function testToString() {
		Assert::same(
			(string)new Filesystem\Size(111, 100),
			'height=100 width=111'
		);
	}
}


(new Size())->run();
