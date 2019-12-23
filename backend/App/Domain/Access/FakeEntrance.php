<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

final class FakeEntrance implements Entrance {
	private ?User $user = null;

	public function __construct(?User $user = null) {
		$this->user = $user;
	}

	public function enter(array $credentials): User {
		return $this->user;
	}

	public function exit(): User {
		return $this->user;
	}
}
