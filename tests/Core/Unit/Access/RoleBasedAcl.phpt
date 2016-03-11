<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Core\Access;

require __DIR__ . '/../../../bootstrap.php';

final class RoleBasedAcl extends \Tester\TestCase {
	public function testFormat() {
		$acl = new Access\RoleBasedAcl(
			new Fake\Configuration([
				'acl' =>
					['guest' => 'yes , yeah, ok,good,okey,yes,,  ,ok,gut,',],
			]),
			'guest'
		);
		Assert::same(['yes', 'yeah', 'ok', 'good', 'okey', 'gut'], $acl->list());
	}

	/**
	* @throws RuntimeException Key does not exist
	*/
	public function testUndefinedRole() {
		$acl = new Access\RoleBasedAcl(
			new Fake\Configuration(['acl' => ['foooo' => 'yes,']]),
			'guest'
		);
		$acl->list();
	}
}


(new RoleBasedAcl())->run();
