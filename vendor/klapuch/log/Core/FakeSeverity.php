<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Fake
 */
final class FakeSeverity implements Severity {
	private $level;

	public function __construct(string $level = null) {
		$this->level = $level;
	}

	public function level(): string {
		return $this->level;
	}
}