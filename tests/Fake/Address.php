<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Http;

final class Address implements Http\Address {
	private $pathname;
	private $basename;
	private $pathnameCounter = 0;
	private $basenameCounter = 0;

	public function __construct(array $pathname = [], string $basename = '') {
		$this->pathname = $pathname;
		$this->basename = $basename;
	}
	
	public function pathname(): array {
		$this->pathnameCounter += 1;
		return $this->pathname;
	}

	public function basename(): string {
		$this->basenameCounter += 1;
		return $this->basename;
	}

	public function pathnameCounter(): int {
		return $this->pathnameCounter;
	}

	public function basenameCounter(): int {
		return $this->basenameCounter;
	}
}