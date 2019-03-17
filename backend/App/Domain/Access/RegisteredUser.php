<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Sql;
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

	public function edit(array $properties): void {
		if ($this->exists('username', $properties['username'])) {
			throw new \UnexpectedValueException(t('access.username.exists', $properties['username']));
		}
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PreparedUpdate(new Sql\AnsiUpdate('users')))
				->set($properties)
				->where('id = :id', ['id' => $this->id()])
		))->execute();
	}

	private function registered(int $id): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(SELECT 1 FROM users WHERE id = :id)',
			['id' => $id],
		))->field();
	}

	private function exists(string $column, string $value): bool {
		return (bool) (new Storage\BuiltQuery(
			$this->connection,
			(new Sql\AnsiSelect(['1']))
				->from(['users'])
				->where(sprintf('%s = :value', $column), ['value' => $value])
				->where('id != :id', ['id' => $this->id])
		))->field();
	}
}
