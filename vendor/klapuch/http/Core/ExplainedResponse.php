<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Response errors explained by user
 */
final class ExplainedResponse implements Response {
	private $origin;
	private $explanation;

	public function __construct(Response $origin, string $explanation) {
		$this->origin = $origin;
		$this->explanation = $explanation;
	}

	public function body(): string {
		try {
			return $this->origin->body();
		} catch (\Throwable $ex) {
			throw new $ex($this->explanation, 0, $ex);
		}
	}

	public function headers(): array {
		try {
			return $this->origin->headers();
		} catch (\Throwable $ex) {
			throw new $ex($this->explanation, 0, $ex);
		}
	}

	public function code(): int {
		try {
			return $this->origin->code();
		} catch (\Throwable $ex) {
			throw new $ex($this->explanation, 0, $ex);
		}
	}
}
