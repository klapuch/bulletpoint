<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Filesystem;

final class Path implements Filesystem\Path {
	private $folder;
	private $file;
	private $extension;

	public function __construct(
		string $folder,
		string $file,
		string $extension
	) {
		$this->folder = $folder;
		$this->file = $file;
		$this->extension = $extension;
	}

	public function folder(): string {
		return $this->folder;
	}

	public function file(): string {
		return $this->file;
	}

	public function extension(): string {
		return $this->extension;
	}

	public function full(): string {
		return $this->folder . $this->file . $this->extension;
	}
}