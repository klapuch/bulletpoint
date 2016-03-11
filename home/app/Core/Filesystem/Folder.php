<?php
namespace Bulletpoint\Core\Filesystem;

final class Folder implements Storage {
	private $folder;

	public function __construct(string $folder) {
		$this->folder = $folder;
	}

	public function save(File $file, string $filename): bool {
		@mkdir($this->folder, 0777, true);
		@unlink($this->path($filename)); // rewrite
		return file_put_contents(
			$this->path($filename),
			$file->content()
		);
	}

	public function load(string $filename): File {
		return new SavedFile(
			new ExistingPath(
				new StandardizedPath(
					$this->path($filename)
				)
			)
		);
	}

	private function path(string $filename): string {
		return $this->folder . '/' . $filename;
	}
}