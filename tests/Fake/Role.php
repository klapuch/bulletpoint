<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Access;

final class Role implements Access\Role {
	private $current;
	private $rank;

	public function __construct(string $current = null, int $rank = null) {
		$this->current = $current;
		$this->rank = $rank;
	}

	public function __toString() {
		return $this->current;
	}

	public function degrade(): Access\Role {
		return $this;
	}

	public function promote(): Access\Role {
		return $this;
	}

	public function rank(): int {
		return (int)$this->rank;
	}
}