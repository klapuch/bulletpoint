<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Justified severity
 */
final class JustifiedSeverity implements Severity {
	private const LEVELS = [
		self::INFO,
		self::WARNING,
		self::ERROR,
	];
	private $level;

	public function __construct(string $level = self::INFO) {
		$this->level = $level;
	}

	public function level(): string {
		if ($this->justified())
			return $this->level;
		throw new \InvalidArgumentException(
			sprintf(
				'Justified levels for severity are %s - "%s" given',
				$this->readableLevels(),
				$this->level
			)
		);
	}

	/**
	 * Is the level justified?
	 * @return bool
	 */
	private function justified(): bool {
		return in_array($this->level, self::LEVELS);
	}

	/**
	 * Human readable levels
	 * @return string
	 */
	private function readableLevels(): string {
		return implode(', ', self::LEVELS);
	}
}