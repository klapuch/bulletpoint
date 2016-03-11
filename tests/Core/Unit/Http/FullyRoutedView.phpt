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

final class FullyRoutedView extends \Tester\TestCase {
	private $routedView;

	protected function setUp() {
		$this->routedView = new Http\FullyRoutedView(
			new Fake\Path('folder/', '%s', '.phtml'),
			new Fake\RoutedView('view'),
			new Fake\RoutedPage('page')
		);
	}

	public function testPath() {
		Assert::same(
			$this->routedView->view(),
			'folder/page/view.phtml'
		);
	}
}


(new FullyRoutedView())->run();
