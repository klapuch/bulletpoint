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

final class ReliablyRoutedPage extends TestCase\Filesystem {
	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
	}

	public function testValidPath() {
		$routedPage = new Http\ReliablyRoutedPage(
			new Fake\Path('temp/', '%s', '.php'),
			new Fake\RoutedPage('page')
		);
		Assert::same(
			$routedPage->page(),
			'page'
		);
	}

	/** 
	* @throws RuntimeException
	*/
	public function testInvalidPath() {
		$routedPage = new Http\ReliablyRoutedPage(
			new Fake\Path('temp/', '%s', '.php'),
			new Fake\RoutedPage('invalidPage')
		);
		$routedPage->page();
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(__DIR__ . '/temp');
		file_put_contents(__DIR__ . '/temp/page.php', 'data');
	}
}


(new ReliablyRoutedPage())->run();
