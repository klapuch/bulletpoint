<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\{Access, Wiki};

final class MySqlBulletpointRating implements Rating {
	private $bulletpoint;
	private $myself;
	private $database;

	public function __construct(
		Wiki\Bulletpoint $bulletpoint,
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->bulletpoint = $bulletpoint;
		$this->myself = $myself;
		$this->database = $database;
	}

	public function increment() {
		$this->change(self::PROS);
	}

	public function decrement() {
		$this->change(self::CONS);
	}

	public function pros(): int {
		return $this->value(self::PROS);
	}

	public function cons(): int {
		return $this->value(self::CONS);
	}

	private function value(string $rating): int {
		return $this->database->fetchColumn(
			'SELECT COUNT(ID)
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ? AND rating = ?',
			[$this->bulletpoint->id(), $rating]
		);
	}

	private function change(string $rating) {
		if($this->isReset($rating))
			$rating = self::NEUTRAL;
		$this->database->query(
			'INSERT INTO bulletpoint_ratings 
			(user_id, rating, bulletpoint_id)
			VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = ?',
			[
				$this->myself->id(),
				$rating,
				$this->bulletpoint->id(),
				$rating
			]
		);
	}

	private function isReset(string $rating): bool {
		return (bool)$this->database->fetch(
			'SELECT 1
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ? AND rating = ? AND user_id = ?',
			[$this->bulletpoint->id(), $rating, $this->myself->id()]
		);
	}
}