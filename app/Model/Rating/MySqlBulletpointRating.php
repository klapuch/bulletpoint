<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Access, Wiki, Storage
};

final class MySqlBulletpointRating implements Rating {
    const NEUTRAL = '0';
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

    public function increase() {
        $this->rate(self::PROS);
    }

    public function decrease() {
        $this->rate(self::CONS);
    }

    public function pros(): int {
        return $this->total(self::PROS);
    }

    public function cons(): int {
        return $this->total(self::CONS);
    }

    private function total(string $rating): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(ID)
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ? AND rating = ?',
            [$this->bulletpoint->id(), $rating]
        );
    }

    private function rate(string $rating) {
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
                $rating,
            ]
        );
    }

    private function isReset(string $rating): bool {
        return $rating === self::NEUTRAL
        || (bool)$this->database->fetch(
            'SELECT 1
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ? AND rating = ? AND user_id = ?',
            [$this->bulletpoint->id(), $rating, $this->myself->id()]
        );
    }
}