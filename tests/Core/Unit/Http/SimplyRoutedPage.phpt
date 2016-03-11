<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Core\Http;

require __DIR__ . '/../../../bootstrap.php';

final class SimplyRoutedPage extends \Tester\TestCase {
	public function testUnavailabilityAndJumpToDefault() {
		Assert::same(
			(new Http\SimplyRoutedPage(new Fake\Address([])))->page(),
			'default'
		);
	}

	/**
	* @throws RuntimeException Page default is not explicilty allowed
	*/
	public function testExplicitlyRefusedPage() {
		(new Http\SimplyRoutedPage(new Fake\Address(['default', 'view'])))->page();
	}

	public function testRouteFromAddress() {
		Assert::same(
			(new Http\SimplyRoutedPage(new Fake\Address(['page', 'view'])))->page(),
			'page'
		);
	}

	public function testEmptyRouteFromAddress() {
		Assert::same(
			(new Http\SimplyRoutedPage(new Fake\Address(['', 'view'])))->page(),
			'default'
		);
	}
}


(new SimplyRoutedPage())->run();
