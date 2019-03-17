<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

/**
 * User from session storage
 */
final class SessionUser implements User {
	/** @var \Bulletpoint\Domain\Access\User */
	private $origin;

	public function __construct(User $origin) {
		$this->origin = $origin;
	}

	public function id(): string {
		return session_id();
	}

	public function properties(): array {
		return ['expiration' => (int) ini_get('session.gc_maxlifetime')] + $this->origin->properties();
	}

	public function edit(array $properties): void {
		$this->origin->edit($properties);
	}
}
