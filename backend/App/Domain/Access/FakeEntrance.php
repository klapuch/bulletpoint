<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

final class FakeEntrance implements Entrance {
	/** @var \Bulletpoint\Domain\Access\User|null */
	private $user;

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
