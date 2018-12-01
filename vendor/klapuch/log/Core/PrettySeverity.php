<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Pretty formatted severity
 */
final class PrettySeverity implements Severity {
	private const UNKNOWN_FORMAT = '|';
	private const UNKNOWN_LEVEL = 'UNSPECIFIED';
	private const FORMATS = [
		self::INFO => '?',
		self::WARNING => '#',
		self::ERROR => '!',
	];
	private $origin;

	public function __construct(Severity $origin) {
		$this->origin = $origin;
	}

	public function level(): string {
		return $this->prettify($this->origin->level());
	}

	/**
	 * Make the level good looking
	 * @param string $level
	 * @return string
	 */
	private function prettify(string $level): string {
		if (array_key_exists($level, self::FORMATS))
			return $this->wrap(self::FORMATS[$level], $level);
		return $this->wrap(self::UNKNOWN_FORMAT, $level ?: self::UNKNOWN_LEVEL);
	}

	/**
	 * Wrap the level to the tag
	 * @param string $tag
	 * @param string $level
	 * @return string
	 */
	private function wrap(string $tag, string $level): string {
		return sprintf('%1$s %2$s %1$s', $tag, $level);
	}
}