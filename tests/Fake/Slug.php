<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Translation;

final class Slug implements Translation\Slug {
	private $origin;
	private $string;

	public function __construct(int $origin = 0, string $string = '') {
		$this->origin = $origin;
		$this->string = $string;
	}

	public function origin(): int {
		return $this->origin;
	}

	public function rename(string $newSlug): Translation\Slug {
		return $this;
	}

	public function __toString() {
		return $this->string;
	}
}