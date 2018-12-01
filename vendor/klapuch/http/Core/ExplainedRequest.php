<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Request errors explained by user
 */
final class ExplainedRequest implements Request {
	private $origin;
	private $explanation;

	public function __construct(Request $origin, string $explanation) {
		$this->origin = $origin;
		$this->explanation = $explanation;
	}

	public function send(): Response {
		try {
			return $this->origin->send();
		} catch (\Throwable $ex) {
			throw new $ex($this->explanation, 0, $ex);
		}
	}
}
