<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

final class RegisteredUser implements User {
	/** @var int */
	private $id;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(int $id, Storage\Connection $connection) {
		$this->id = $id;
		$this->connection = $connection;
	}

	/**
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	public function properties(): array {
		$user = (new Storage\TypedQuery(
			$this->connection,
			'SELECT * FROM users WHERE id = :id',
			['id' => $this->id()],
		))->row();
		return (new ConstantUser((string) $user['id'], $user))->properties();
	}

	/**
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	public function id(): string {
		if ($this->registered($this->id))
			return (string) $this->id;
		throw new \UnexpectedValueException('The user has not been registered yet');
	}

	private function registered(int $id): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM users WHERE id = :id)',
			['id' => $id],
		))->field();
	}
}
