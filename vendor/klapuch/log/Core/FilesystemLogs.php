<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Logs stored on the filesystem
 */
final class FilesystemLogs implements Logs {
	private $location;

	public function __construct(Location $location) {
		$this->location = $location;
	}

	public function put(Log $log): void {
		file_put_contents(
			$this->location->path(),
			$log->description(),
			LOCK_EX | FILE_APPEND
		);
	}
}