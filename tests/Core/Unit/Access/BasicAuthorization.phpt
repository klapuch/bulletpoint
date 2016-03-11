<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Access;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class BasicAuthorization extends \Tester\TestCase {
	private $authorization;

	protected function setUp() {
		$this->authorization = new Access\BasicAuthorization(
			new Fake\Acl([
				'secret',
				'admin/setting',
				'delete',
				'create',
				'',
			]),
			new Fake\Comparison
		);
	}

	protected function allowedUrls() {
		return [
			[['registration', 'myself']],
			[['login']],
			[['']],
			[[]],
		];
	}

	protected function forbiddenUrls() {
		return [
			[['secret']],
			[['admin', 'setting']],
			[['delete']],
		];
	}

	/**
	* @dataProvider allowedUrls
	*/
	public function testAllowedAccess($url) {
		Assert::true(
			$this->authorization->hasAccess(
				new Fake\Address($url)
			)
		);
	}

	/**
	* @dataProvider forbiddenUrls
	*/
	public function testForbiddenAccess($url) {
		Assert::false(
			$this->authorization->hasAccess(
				new Fake\Address($url)
			)
		);
	}
}


(new BasicAuthorization())->run();
