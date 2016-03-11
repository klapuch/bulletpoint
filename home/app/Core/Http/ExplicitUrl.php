<?php
namespace Bulletpoint\Core\Http;

final class ExplicitUrl implements Address {
	private $pathname;
	private $basename;

	public function __construct(string $pathname = null, string $basename = null) {
		$this->pathname = $pathname;
		$this->basename = $basename;
	}

	public function pathname(): array {
		return array_values(array_filter(explode('/', $this->pathname)));
	}

	public function basename(): string {
		return (string)$this->basename;
	}
}