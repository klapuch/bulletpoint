<?php
declare(strict_types = 1);

namespace Bulletpoint\Http;

use Bulletpoint\Domain\Access;

/**
 * Chosen role from the listed ones
 */
final class ChosenRole implements Role {
	private Access\User $user;

	/** @var mixed[] */
	private array $roles;

	public function __construct(Access\User $user, array $roles) {
		$this->user = $user;
		$this->roles = $roles;
	}

	public function allowed(): bool {
		return (bool) array_uintersect(
			[$this->user->properties()['role'] ?? 'guest'],
			$this->roles,
			'strcasecmp',
		);
	}
}
