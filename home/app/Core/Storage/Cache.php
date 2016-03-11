<?php
namespace Bulletpoint\Core\Storage;

interface Cache {
	public function save(string $key, $data);
	public function load(string $key);
}