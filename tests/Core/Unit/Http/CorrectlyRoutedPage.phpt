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

final class CorrectlyRoutedPage extends \Tester\TestCase {
	private $routedPage;

	protected function setUp() {
		$this->routedPage = new Http\CorrectlyRoutedPage(
			new Fake\RoutedPage('page'),
			new Fake\Correction
		);
	}

	public function testFormat() {
		Assert::same($this->routedPage->page(), 'page');
	}
}


(new CorrectlyRoutedPage())->run();
