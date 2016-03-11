<?php
namespace Bulletpoint\Core\Filesystem;

final class SavedFile implements File {
	private $path;

	public function __construct(Path $path) {
		$this->path = $path;
	}

	public function name(): string {
		return pathinfo($this->path->full(), PATHINFO_BASENAME);
	}

	public function type(): string {
		return finfo_file(
			finfo_open(FILEINFO_MIME_TYPE),
			$this->path->full()
		);
	}

	public function size(): int {
		return filesize($this->path->full());
	}

	public function content(): string {
		return file_get_contents($this->path->full());
	}

	public function location(): string {
		return $this->path->full();
	}
}