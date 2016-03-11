<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Report;

final class Target implements Report\Target {
	private $id;

	public function __construct(int $id = 0) {
		$this->id = $id;
	}

	public function id(): int {
		return $this->id;
	}

	public function complaints(): \Iterator {
		return [];
	}
}