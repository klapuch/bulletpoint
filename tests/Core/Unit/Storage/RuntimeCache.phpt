<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Storage;

require __DIR__ . '/../../../bootstrap.php';

final class RuntimeCache extends \Tester\TestCase {
	private $cache;

	protected function setUp() {
		$this->cache = new Storage\RuntimeCache();
	}

	public function testLoadingValidKey() {
		$this->cache->save('myKey', 'fooData');
		Assert::same('fooData', $this->cache->load('myKey'));
	}

	public function testLoadingInvalidKey() {
		$this->cache->save('foooooKeeeey', 'fooData');
		Assert::same(null, $this->cache->load('unknownKey'));
	}
}


(new RuntimeCache())->run();
