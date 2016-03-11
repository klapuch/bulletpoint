<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\Storage;

final class CachedAddress implements Address {
	private $address;
	private $cache;

	public function __construct(Address $address, Storage\Cache $cache) {
		$this->address = $address;
		$this->cache = $cache;
	}

	public function pathname(): array {
		if($this->cache->load(__CLASS__ . __METHOD__) === null)
			$this->cache->save(__CLASS__ . __METHOD__, $this->address->pathname());
		return $this->cache->load(__CLASS__ . __METHOD__);
	}

	public function basename(): string {
		if($this->cache->load(__CLASS__ . __METHOD__) === null)
			$this->cache->save(__CLASS__ . __METHOD__, $this->address->basename());
		return $this->cache->load(__CLASS__ . __METHOD__);
	}
}