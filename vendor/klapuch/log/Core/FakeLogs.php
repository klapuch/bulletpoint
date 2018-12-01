<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Fake
 */
final class FakeLogs implements Logs {
	private $location;

	public function __construct(string $location = null) {
		$this->location = $location;
	}

	public function put(Log $log): void {
		if ($this->location)
			file_put_contents($this->location, $log->description());
	}
}