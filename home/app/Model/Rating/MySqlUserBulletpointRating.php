<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\{Access, Wiki};

final class MySqlUserBulletpointRating implements Rating {
	private $bulletpoint;
	private $myself;
	private $database;
	private $origin;

	public function __construct(
		Wiki\Bulletpoint $bulletpoint,
		Access\Identity $myself,
		Storage\Database $database,
		Rating $origin
	) {
		$this->bulletpoint = $bulletpoint;
		$this->myself = $myself;
		$this->database = $database;
		$this->origin = $origin;
	}

	public function increment() {
		$this->origin->increment();
	}

	public function decrement() {
		$this->origin->decrement();
	}

	public function pros(): int {
		return $this->value(self::PROS);
	}

	public function cons(): int {
		return $this->value(self::CONS);
	}

	private function value(string $rating): int {
		return (int)$this->database->fetch(
			'SELECT 1
			FROM bulletpoint_ratings
			WHERE user_id = ?
			AND bulletpoint_id = ? AND rating = ?',
			[$this->myself->id(), $this->bulletpoint->id(), $rating]
		);
	}
}