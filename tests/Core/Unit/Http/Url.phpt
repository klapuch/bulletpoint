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

final class Url extends \Tester\TestCase {
	protected function basenames() {
		return [
			['/Acme/www/index.php', '/Acme/www/'],
			['/Acme/index.php', '/Acme/'],
			['/Spot/2/Meet/www/index.php', '/Spot/2/Meet/www/'],
			['/index.php', '/'],
		];
	}

	/**
	* @dataProvider basenames
	*/
	public function testBasename($scriptUrl, $expected) {
		Assert::same((new Http\Url($scriptUrl, ''))->basename(), $expected);
	}

	protected function pathnames() {
		return [
			['/Acme/www/index.php', '/Acme/www/a/b/c', ['a', 'b', 'c']],
			['/Acme/index.php', '/Acme/a/b/c', ['a', 'b', 'c']],
			['/Spot/2/Meet/www/index.php', '/Spot/2/Meet/www/a/b/c', ['a', 'b', 'c']],
			['/index.php', '/a/b/c', ['a', 'b', 'c']],
			['/Acme/www/index.php', '/Acme/www/registration', ['registration']],
			['/Acme/www/index.php', '/acme/www/page/view', ['page', 'view']], // case sensitive bug
			['/acme/www/index.php', '/Acme/www/page/view', ['page', 'view']], // case sensitive bug
			['/acme/www/index.php', '/acme/www/page/view/?get=someValue', ['page', 'view']],
			['/acme/www/index.php', '/acme/www/page/?get=someValue', ['page']],
			['/acme/www/index.php', '/acme/www/page/view?get=someValue', ['page']],
			['/acme/www/index.php', '/acme/www/page/view/1/?get=someValue', ['page', 'view', '1']],
			['/acme/www/index.php', '/acme/www/page/view/', ['page', 'view']], // strlen check - /
			['index.php', '/page/view/', ['page', 'view']],
			['/index.php/', '/page/view/', ['page', 'view']],
			['/index.php/', '/page/view/view/page', ['page', 'view','view', 'page']], // there may be duplication in name
			['/bulletpoint/www/index.php', '/bulletpoint/www/bulletpoint/upravit/5', ['bulletpoint', 'upravit', '5']], 
		];
	}

	/**
	* @dataProvider pathnames
	*/
	public function testPathname($scriptUrl, $realUrl, $expected) {
		Assert::same((new Http\Url($scriptUrl, $realUrl))->pathname(), $expected);
	}
}


(new Url())->run();
