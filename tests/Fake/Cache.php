<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Storage;

final class Cache implements Storage\Cache {
	private $cache = [];

	public function __construct(array $cache) {
		$this->cache = $cache;
	}

	public function save(string $key, $data) {
		$this->cache[$key] = $data;
	}

	public function load(string $key) {
		if(isset($this->cache[$key]))
			return $this->cache[$key];
	}
}