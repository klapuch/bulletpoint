<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

/**
 * User which can be publicly shown
 */
final class PublicUser implements User {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
	}

	public function id(): string {
		return (string) $this->id;
	}

	public function properties(): array {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT username, avatar_filename FROM users WHERE id = ?',
			[$this->id],
		))->row();
	}

	public function edit(array $properties): void {
	}
}
