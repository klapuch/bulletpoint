<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Core\Http;

require __DIR__ . '/../../../bootstrap.php';

final class ReliablyRoutedView extends TestCase\Filesystem {
	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
	}

	public function testValidPath() {
		$routedView = new Http\ReliablyRoutedView(
			new Fake\Path('temp/', '%s', '.phtml'),
			new Fake\RoutedView('view'),
			new Fake\RoutedPage('page')
		);
		Assert::same(
			$routedView->view(),
			'view'
		);
	}

	/** 
	* @throws RuntimeException
	*/
	public function testInvalidPath() {
		$routedView = new Http\ReliablyRoutedView(
			new Fake\Path('temp/', '%s', '.phtml'),
			new Fake\RoutedView('invalidView'),
			new Fake\RoutedPage('page')
		);
		$routedView->view();
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(__DIR__ . '/temp/page/');
		file_put_contents(__DIR__ . '/temp/page/view.phtml', 'data');
	}
}


(new ReliablyRoutedView())->run();
