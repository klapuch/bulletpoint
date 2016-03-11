<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Security;
use Bulletpoint\Core\Http;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class CsrfProtection extends \Tester\TestCase {
	const KEY = Security\CsrfProtection::KEY; // shorter
	private $emptyAddress;

	public function setUp() {
		$this->emptyAddress = new Fake\Address([]);
	}

	public function testProtection() {
		$session = [];
		Assert::notSame('', (new Security\CsrfProtection(
			new Http\Session($session),
			new Http\Request([], [], $this->emptyAddress)
			)
		)->protection());
	}

	public function testMultipleProtection() {
		$session = [Security\CsrfProtection::KEY => '123456'];
		Assert::same(
			'123456',
			(new Security\CsrfProtection(
				new Http\Session($session),
				new Http\Request(
					[],
					[],
					$this->emptyAddress
				)
			))->protection()
		);
	}

	/**
	* @throws Bulletpoint\Exception\CsrfException Timeout
	*/
	public function testPostTimeout() {
		$session = [Security\CsrfProtection::KEY => 'yyy'];
		(new Security\CsrfProtection(
			new Http\Session($session),
			new Http\Request(
				[],
				[Security\CsrfProtection::KEY => 'x'],
				$this->emptyAddress
			)
		))->defend();
	}

	/**
	* @throws Bulletpoint\Exception\CsrfException Timeout
	*/
	public function testGetTimeout() {
		$session = [Security\CsrfProtection::KEY => 'yyy'];
		(new Security\CsrfProtection(
			new Http\Session($session),
			new Http\Request(
				[Security\CsrfProtection::KEY => 'x'],
				[],
				$this->emptyAddress
			)
		))->defend();
	}

	/**
	* @throws Bulletpoint\Exception\CsrfException Timeout
	*/
	public function testUnknownMethod() {
		$session = [];
		(new Security\CsrfProtection(
			new Http\Session($session),
			new Http\Request(
				[Security\CsrfProtection::KEY => 'x'],
				[],
				$this->emptyAddress
			)
		))->defend();
	}

	public function testWithoutTimeout() {
		$session = [Security\CsrfProtection::KEY => 'x'];
		(new Security\CsrfProtection(
			new Http\Session($session),
			new Http\Request(
				[Security\CsrfProtection::KEY => 'x'],
				[],
				$this->emptyAddress
			)
		))->defend();
		Assert::true(true);
	}

	public function testKey() {
		$session = [];
		Assert::same(
			Security\CsrfProtection::KEY, 
			(new Security\CsrfProtection(
				new Http\Session($session),
				new Http\Request([], [], $this->emptyAddress)))->key()
		);
	}
}


(new CsrfProtection())->run();
