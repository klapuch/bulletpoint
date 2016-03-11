<?php
namespace Bulletpoint\Model\User;

final class Applicant {
	private $user;
	private $email;

	public function __construct(User $user, string $email) {
		$this->user = $user;
		$this->email = $email;
	}

	public function username(): string {
		return $this->user->username();
	}

	public function password(): string {
		return $this->user->password();
	}

	public function email(): string {
		return $this->email;
	}
}