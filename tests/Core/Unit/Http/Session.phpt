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

final class Session extends \Tester\TestCase {
	private $storage = [];
	private $session;

	protected function setUp() {
		$this->session = new Http\Session($this->storage);
	}

	public function testMutableStorage() {
		$this->session['test'] = 'value';
		Assert::same($this->storage, ['test' => 'value']);
	}

	public function testSet() {
		$this->session[] = 10;
		$this->session['key'] = 10;
		Assert::same($this->storage, [0 => 10, 'key' => 10]);
	}

	public function testGet() {
		$this->session['key'] = 10;
		Assert::same($this->session['key'], 10);
		Assert::same($this->session['Key'], null);
		Assert::same($this->session['foo'], null);
	}

	public function testUnset() {
		$this->session[] = 100;
		Assert::same($this->storage, [0 => 100]);
		unset($this->session[0]);
		Assert::same($this->storage, []);
	}
}


(new Session())->run();
