<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Fake
 */
final class FakeLog implements Log {
	private $description;

	public function __construct(string $description = null) {
		$this->description = $description;
	}

	public function description(): string {
		return $this->description;
	}
}