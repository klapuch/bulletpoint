<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Available HTTP response with status code lower than 4xx
 */
final class AvailableResponse implements Response {
	private $origin;

	public function __construct(Response $origin) {
		$this->origin = $origin;
	}

	public function body(): string {
		if ($this->available($this->code()))
			return $this->origin->body();
		throw new \UnexpectedValueException('The response is not available');
	}

	public function headers(): array {
		return $this->origin->headers();
	}

	public function code(): int {
		return $this->origin->code();
	}

	private function available(int $code): bool {
		return $code < 400;
	}
}
