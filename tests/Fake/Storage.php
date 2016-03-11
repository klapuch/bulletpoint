<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Filesystem;

final class Storage implements Filesystem\Storage {
	private $onSave;
	private $file;

	public function __construct(
		bool $onSave = true,
		Filesystem\File $file = null
	) {
		$this->onSave = $onSave;
		$this->file = $file;
	}

	public function save(Filesystem\File $file, string $filename): bool {
		return $this->onSave;
	}

	public function load(string $filename): Filesystem\File {
		return $this->file ?? new File();
	}
}