<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../home/functions.php';

final class Functions extends \Tester\TestCase {
	protected function arrays() {
		// [tested, expected]
		return [
			[[], true],
			[[[[[[[]]]]]], true],
			[['abc'], true],
			[[0], true],
			[[null], true],
			[null, false],
			[false, false],
			[true, false],
			['abc', false],
			[new TraversableIterator, false]
		];
	}

	/**
	* @dataProvider arrays
	*/
	public function testIsArray($actual, $expected) {
		Assert::same(isArray($actual), $expected);
	}

	public function testLocalhost() {
		Assert::true(isLocalhost('127.0.0.1'));
		Assert::true(isLocalhost('::1'));
		Assert::true(isLocalhost('localhost'));
		Assert::false(isLocalhost('192.168.1.10'));
		Assert::false(isLocalhost('LOCALHOST'));
	}
}

class TraversableIterator implements \Iterator {
	public function current() {

	}

	public function key() {

	}

	public function next() {

	}

	public function rewind() {

	}

	public function valid() {

	}
}


(new Functions())->run();
