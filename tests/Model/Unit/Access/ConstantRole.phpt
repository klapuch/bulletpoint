<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Access;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class ConstantRole extends \Tester\TestCase {
	public function testEmptyRole() {
		Assert::same(
			'guest',
			(string)new Access\ConstantRole('', new Fake\Role('creator'))
		);
	}
}


(new ConstantRole())->run();
