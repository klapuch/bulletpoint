<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Raw HTTP response
 */
final class RawResponse implements Response {
	private const CODES = [100, 599];
	private const PROTOCOL = 0,
		CODE = 1;
	private const EMPTY_HEADERS = [];
	private $headers;
	private $body;

	public function __construct(array $headers, string $body) {
		$this->headers = $headers;
		$this->body = $body;
	}

	public function body(): string {
		return $this->body;
	}

	public function headers(): array {
		$headers = array_reduce(
			array_filter(
				$this->headers,
				function($header): bool {
					return strpos($header, ':') !== false;
				}
			),
			function(array $headers, string $header): array {
				[$field, $value] = explode(':', $header, 2);
				$headers[$field] = trim($value);
				return $headers;
			},
			self::EMPTY_HEADERS
		);
		if ($headers)
			return $headers;
		throw new \UnexpectedValueException('Headers of the response are empty');
	}

	public function code(): int {
		$status = $this->status();
		if ($this->isCode($status[self::CODE]))
			return $status[self::CODE];
		throw new \UnexpectedValueException(
			sprintf(
				'Allowed range for the status codes is %sxx - %sxx',
				substr((string) self::CODES[0], 0, 1),
				substr((string) self::CODES[1], 0, 1)
			)
		);
	}

	/**
	 * Does the given code belongs to valid status codes?
	 * @param int $code
	 * @return bool
	 */
	private function isCode(int $code): bool {
		return in_array($code, range(...self::CODES));
	}

	/**
	 * Status of the response, if any
	 * @throws \Exception
	 * @return array
	 */
	private function status(): array {
		$status = explode(' ', current($this->headers));
		if (count($status) < 3
		|| strcasecmp(substr($status[self::PROTOCOL], 0, 4), 'http')) {
			throw new \UnexpectedValueException(
				'Status code of the response is not known'
			);
		}
		$status[self::CODE] = (int) $status[self::CODE];
		return $status;
	}
}
