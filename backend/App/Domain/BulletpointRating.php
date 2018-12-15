<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
use Klapuch\Storage;

final class BulletpointRating implements Rating {
	/** @var int */
	private $bulletpoint;

	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(int $bulletpoint, Access\User $user, Storage\Connection $connection) {
		$this->bulletpoint = $bulletpoint;
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 * @param int $point
	 */
	public function rate(int $point): void {
		(new Storage\BuiltQuery(
			$this->connection,
			(new Sql\PgInsertInto(
				'bulletpoint_ratings',
				['point' => ':point', 'user_id' => ':user_id', 'bulletpoint_id' => ':bulletpoint_id'],
				['point' => $point, 'user_id' => $this->user->id(), 'bulletpoint_id' => $this->bulletpoint]
			))->onConflict(['user_id', 'bulletpoint_id'])
				->doUpdate(['point' => 'EXCLUDED.point'])
		))->execute();
	}
}
