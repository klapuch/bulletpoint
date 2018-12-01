<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Fake
 */
final class FakeLocation implements Location {
	private $path;

	public function __construct(string $path) {
		$this->path = $path;
	}

	public function path(): string {
		return $this->path;
	}
}