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

final class CorrectlyRoutedView extends \Tester\TestCase {
	private $routedView;

	protected function setUp() {
		$this->routedView = new Http\CorrectlyRoutedView(
			new Fake\RoutedView('view'),
			new Fake\Correction
		);
	}

	public function testFormat() {
		Assert::same($this->routedView->view(), 'view');
	}
}


(new CorrectlyRoutedView())->run();
