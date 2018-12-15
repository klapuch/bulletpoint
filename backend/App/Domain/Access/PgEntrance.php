<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

/**
 * Entrance to PG database
 */
final class PgEntrance implements Entrance {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\Entrance */
	private $origin;

	public function __construct(Entrance $origin, Storage\Connection $connection) {
		$this->origin = $origin;
		$this->connection = $connection;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		(new Storage\NativeQuery($this->connection, 'SELECT globals_set_user(?)', [$user->id()]))->execute();
		return $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function exit(): User {
		(new Storage\NativeQuery($this->connection, 'SELECT globals_set_user(NULL)'))->execute();
		return $this->origin->exit();
	}
}
