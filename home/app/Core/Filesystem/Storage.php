<?php
namespace Bulletpoint\Core\Filesystem;

interface Storage {
	public function save(File $file, string $filename): bool;
	public function load(string $filename): File;
}