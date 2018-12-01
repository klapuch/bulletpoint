<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Fake
 */
final class FakeResponse implements Response {
	private $body;
	private $headers;
	private $code;
	private $ex;

	public function __construct($body = null, $headers = null, $code = null, \Throwable $ex = null) {
		$this->body = $body;
		$this->headers = $headers;
		$this->code = $code;
		$this->ex = $ex;
	}

	public function body(): string {
		if ($this->ex === null)
			return $this->body;
		throw $this->ex;
	}

	public function headers(): array {
		if ($this->ex === null)
			return $this->headers;
		throw $this->ex;
	}

	public function code(): int {
		if ($this->ex === null)
			return $this->code;
		throw $this->ex;
	}
}
