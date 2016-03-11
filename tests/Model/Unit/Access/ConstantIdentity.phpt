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

final class ConstantIdentity extends \Tester\TestCase {
	public function testId() {
		Assert::same(
			100,
			(new Access\ConstantIdentity(100, new Fake\Role, 'facedown'))->id()
		);
	}

	public function testRole() {
		Assert::equal(
			(new Access\ConstantIdentity(100, new Fake\Role('admin'), 'facedown'))
			->role(),
			new Fake\Role('admin')
		);
	}

	public function testUsername() {
		Assert::same(
			'facedown',
			(new Access\ConstantIdentity(100, new Fake\Role, 'facedown'))
			->username()
		);
	}
}


(new ConstantIdentity())->run();
