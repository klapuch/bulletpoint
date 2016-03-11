<?php
namespace Bulletpoint\Core\Filesystem;

final class SprintfPath implements Path {
	private $path;
	const PLACEHOLDER = '%s';

	public function __construct(Path $path) {
		$this->path = $path;
	}

	public function folder(): string {
		if($this->path->folder())
			return $this->path->folder();
		return self::PLACEHOLDER;
	}

	public function file(): string {
		if($this->path->file())
			return $this->path->file();
		return self::PLACEHOLDER;
	}

	public function extension(): string {
		if($this->path->extension())
			return $this->path->extension();
		return self::PLACEHOLDER;
	}

	public function full(): string {
		return $this->folder() . $this->file() . $this->extension();
	}
}