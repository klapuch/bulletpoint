<?php
namespace Bulletpoint\Model\Access;

final class ConstantIdentity implements Identity {
	private $id;
	private $role;
	private $username;

	public function __construct(int $id, Role $role, string $username) {
		$this->id = $id;
		$this->role = $role;
		$this->username = $username;
	}

	public function id(): int {
		return $this->id;
	}

	public function role(): Role {
		return $this->role;
	}

	public function username(): string {
		return $this->username;
	}
}