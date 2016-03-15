<?php
namespace Bulletpoint\Core\Storage;

final class RuntimeCache implements Cache {
	private $cache = [];

	public function save(string $key, $data) {
		$this->cache[$key] = $data;
	}

	public function load(string $key) {
		if($this->isInCache($key))
			return $this->cache[$key];
	}

	private function isInCache(string $key): bool {
		return isset($this->cache[$key]);
	}
}