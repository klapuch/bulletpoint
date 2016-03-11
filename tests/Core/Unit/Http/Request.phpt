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

final class Request extends \Tester\TestCase {
	private $post = [
		null,
		false,
		'foo',
		'abc   ',
		'   abc',
		'   abc   ',
		"abc\r\n",
		'abc00',
		'000',
		'abc   0',
		0
	];
	private $get = [null, false, 'foo'];
	private $request;

	protected function setUp() {
		$this->request = new Http\Request(
			$this->get,
			$this->post,
			new Fake\Address(['x'], 'y')
		);
	}

	protected function getRequests() {
		return [
			[null, [null, false, 'foo']],
			['blah', null],
			[0, null],
			[1, false],
		];
	}

	protected function postRequests() {
		return [
			[null, ['', '', 'foo', 'abc', 'abc', 'abc', 'abc', 'abc00', '000', 'abc   0', '0']],
			['blah', ''],
			[0, ''],
			[1, ''],
		];
	}

	/**
	* @dataProvider postRequests
	*/
	public function testPost($request, $expectation) {
		Assert::same($this->request->post($request), $expectation);
	}

	/**
	* @dataProvider getRequests
	*/
	public function testGet($request, $expectation) {
		Assert::same($this->request->get($request), $expectation);
	}

	public function testAddress() {
		Assert::type('Bulletpoint\Core\Http\Address', $this->request->address());
	}
}


(new Request())->run();
