<?php
declare(strict_types = 1);
namespace Klapuch\Http;

/**
 * Fake
 */
final class FakeRequest implements Request {
	private $response;
	private $ex;

	public function __construct(Response $response = null, \Throwable $ex = null) {
		$this->response = $response;
		$this->ex = $ex;
	}

	public function send(): Response {
		if ($this->ex === null)
			return $this->response;
		throw $this->ex;
	}
}
