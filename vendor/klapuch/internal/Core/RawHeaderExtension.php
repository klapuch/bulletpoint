<?php
declare(strict_types = 1);
namespace Klapuch\Internal;

final class RawHeaderExtension implements Extension {
	private $headers;

	public function __construct(array $headers) {
		$this->headers = $headers;
	}

	public function improve(): void {
		foreach ($this->headers as $header)
			header((string) $header);
	}
}