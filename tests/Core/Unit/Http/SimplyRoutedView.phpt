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

final class SimplyRoutedView extends \Tester\TestCase {
	public function testUnavailabilityAndJumpToDefault() {
		Assert::same(
			(new Http\SimplyRoutedView(new Fake\Address(['page'])))->view(),
			'default'
		);
	}

	/**
	* @throws RuntimeException View default is not explicilty allowed
	*/
	public function testExplicitlyRefusedView() {
		(new Http\SimplyRoutedView(new Fake\Address(['page', 'default'])))->view();
	}

	public function testRouteFromAddress() {
		Assert::same(
			(new Http\SimplyRoutedView(new Fake\Address(['page', 'view'])))->view(),
			'view'
		);
	}

	public function testEmptyRouteFromAddress() {
		Assert::same(
			(new Http\SimplyRoutedView(new Fake\Address(['page', ''])))->view(),
			'default'
		);
	}
}


(new SimplyRoutedView())->run();
