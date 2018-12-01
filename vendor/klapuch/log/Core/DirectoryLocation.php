<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Location to the directory
 */
final class DirectoryLocation implements Location {
	private $path;

	public function __construct(string $path) {
		$this->path = $path;
	}

	public function path(): string {
		if (!file_exists($this->path)) {
			throw new \InvalidArgumentException(
				sprintf(
					'Path to directory "%s" does not exist',
					$this->path
				)
			);
		} elseif (!is_dir($this->path) || !is_writable($this->path)) {
			throw new \InvalidArgumentException(
				sprintf(
					'"%s" is not a directory or is not writable',
					$this->path
				)
			);
		}
		return $this->path;
	}
}