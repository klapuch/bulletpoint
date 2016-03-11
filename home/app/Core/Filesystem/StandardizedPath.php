<?php
namespace Bulletpoint\Core\Filesystem;

final class StandardizedPath implements Path {
	private $folder;
	private $file;
	private $extension;
	const SEPARATORS = '\/';

	public function __construct() {
		$args = func_get_args();
        $count = func_num_args();
        if(method_exists($this, '__construct' . $count))
            call_user_func_array([$this, '__construct' . $count], $args);
	}

	private function __construct1(string $fullPath) {
		$this->__construct3(
			(string)pathinfo($fullPath, PATHINFO_DIRNAME),
			(string)pathinfo($fullPath, PATHINFO_FILENAME),
			(string)pathinfo($fullPath, PATHINFO_EXTENSION)
		);
	}

	private function __construct3(
		string $folder,
		string $file,
		string $extension
	) {
		$this->folder = $folder;
		$this->file = $file;
		$this->extension = $extension;
	}

	public function folder(): string {
		return rtrim($this->folder, self::SEPARATORS) . '/';
	}

	public function file(): string {
		return trim($this->file, self::SEPARATORS);
	}

	public function extension(): string {
		if($this->extension)
			return '.' . trim($this->extension, '.');
		return (string)$this->extension;
	}

	public function full(): string {
		return $this->folder() . $this->file() . $this->extension();
	}
}