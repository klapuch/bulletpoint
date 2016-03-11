<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Core\Http;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class CachedAddress extends Tester\TestCase {
	private $cachedAddress;
	private $fakeAddress;

	protected function setUp() {
		$this->fakeAddress = new Fake\Address();
		$this->cachedAddress = new Http\CachedAddress(
			$this->fakeAddress,
			new Fake\Cache([])
		);
	}

	public function testCache() {
		$this->cachedAddress->pathname();
		$this->cachedAddress->pathname();
		$this->cachedAddress->pathname();
		$this->cachedAddress->basename();
		$this->cachedAddress->basename();
		$this->cachedAddress->basename();
		Assert::same($this->fakeAddress->pathnameCounter(), 1);
		Assert::same($this->fakeAddress->basenameCounter(), 1);
	}
}


(new CachedAddress())->run();
