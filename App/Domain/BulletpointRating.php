<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

use Klapuch\Sql;
use Klapuch\Storage;

final class BulletpointRating implements Rating {
	private const UP = 1;
	private const DOWN = -1;
	private const RESET = 0;

	/** @var int */
	private $bulletpoint;

	/** @var Storage\Connection */
	private $connection;

	/** @var User */
	private $user;

	public function __construct(int $bulletpoint, User $user, Storage\Connection $connection) {
		$this->bulletpoint = $bulletpoint;
		$this->connection = $connection;
		$this->user = $user;
	}

	public function up(): void {
		$this->vote(self::UP);
	}

	public function down(): void {
		$this->vote(self::DOWN);
	}

	public function reset(): void {
		$this->vote(self::RESET);
	}

	private function vote(int $point): void {
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