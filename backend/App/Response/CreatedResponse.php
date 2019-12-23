<?php
declare(strict_types = 1);

namespace Bulletpoint\Response;

use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Uri\Uri;

final class CreatedResponse implements Application\Response {
	private Application\Response $origin;
	private Uri $uri;

	public function __construct(Application\Response $origin, Uri $uri) {
		$this->origin = $origin;
		$this->uri = $uri;
	}

	public function body(): Output\Format {
		return $this->origin->body();
	}

	public function headers(): array {
		return ['Location' => $this->uri->reference()] + $this->origin->headers();
	}

	public function status(): int {
		return HTTP_CREATED;
	}
}
