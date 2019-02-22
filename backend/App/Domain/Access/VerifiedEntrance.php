<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Access;

use Klapuch\Storage;

/**
 * Verified entrance
 */
final class VerifiedEntrance implements Entrance {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\Entrance */
	private $origin;

	public function __construct(Storage\Connection $connection, Entrance $origin) {
		$this->connection = $connection;
		$this->origin = $origin;
	}

	/**
	 * @param array $credentials
	 * @throws \UnexpectedValueException
	 * @return \Bulletpoint\Domain\Access\User
	 */
	public function enter(array $credentials): User {
		$user = $this->origin->enter($credentials);
		if (!$this->verified($user))
			throw new \UnexpectedValueException('Email has not been verified yet');
		return $user;
	}

	private function verified(User $user): bool {
		return (new Storage\TypedQuery(
			$this->connection,
			'SELECT EXISTS(
    			SELECT 1 FROM access.verification_codes WHERE user_id = ? AND used_at IS NOT NULL
    		)',
			[$user->id()],
		))->field();
	}

	/**
	 * @return \Bulletpoint\Domain\Access\User
	 * @throws \UnexpectedValueException
	 */
	public function exit(): User {
		return $this->origin->exit();
	}
}
