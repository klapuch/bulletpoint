<?php
declare(strict_types = 1);
namespace Klapuch\Log;

/**
 * Pretty formatted log
 */
final class PrettyLog implements Log {
	private $exception;
	private $severity;

	public function __construct(\Throwable $exception, Severity $severity) {
		$this->exception = $exception;
		$this->severity = $severity;
	}

	public function description(): string {
		return sprintf(
			"%s\r\n\r\n%s",
			$this->prettify($this->exception),
			$this->exception->getTraceAsString()
		);
	}

	/**
	 * Prettified version of the exception
	 * @param \Throwable $exception
	 * @return string
	 */
	private function prettify(\Throwable $exception): string {
		return sprintf(
			'%s - %s - %d - %s',
			(new \DateTimeImmutable())->format('Y-m-d H:i'),
			$this->severity->level(),
			$exception->getCode(),
			$exception->getMessage() ?: 'No message was provided'
		);
	}
}