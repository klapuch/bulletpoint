<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Access, Wiki, Storage
};

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

    public function increase() {
        $this->origin->increase();
    }

    public function decrease() {
        $this->origin->decrease();
    }

    public function pros(): int {
        return $this->total(self::PROS);
    }

    public function cons(): int {
        return $this->total(self::CONS);
    }

    private function total(string $rating): int {
        return (int)$this->database->fetch(
            'SELECT 1
			FROM bulletpoint_ratings
			WHERE user_id = ?
			AND bulletpoint_id = ? AND rating = ?',
            [$this->myself->id(), $this->bulletpoint->id(), $rating]
        );
    }
}