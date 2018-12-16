<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

/**
 * User which can be publicly shown, but includes potential private data to owner
 */
final class PubliclyPrivateUser implements User {
	/** @var \Bulletpoint\Domain\Access\User */
	private $origin;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(User $origin, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
	}

	public function id(): string {
		return $this->origin->id();
	}

	public function properties(): array {
		$properties = (new Storage\TypedQuery(
			$this->connection,
			'SELECT role, email FROM users WHERE id = ?',
			[$this->id()]
		))->row();
		return [
			'email' => $properties['email'],
			'role' => $properties['role'],
		];
	}
}
