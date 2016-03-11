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

final class ExplicitUrl extends \Tester\TestCase {
	protected function pathnames() {
		return [
			['/Acme/www/', ['Acme', 'www']],
			['/Acme/', ['Acme']],
			['/Spot/2/Meet/www/', ['Spot', '2', 'Meet', 'www']],
			['/', []]
		];
	}

	/**
	* @dataProvider pathnames
	*/
	public function testPathname($pathname, $expected) {
		Assert::same((new Http\ExplicitUrl($pathname))->pathname(), $expected);
	}

	public function testBasename() {
		Assert::same((new Http\ExplicitUrl('', '/url/'))->basename(), '/url/');
	}
}


(new ExplicitUrl())->run();
