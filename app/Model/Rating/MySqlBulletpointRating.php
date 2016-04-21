<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Access, Wiki, Storage
};

final class MySqlBulletpointRating implements Rating {
    const NEUTRAL = 0;
    const PROS = +1;
    const CONS = -1;
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

    public function points(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID, point, user_id
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ?',
            [$this->bulletpoint->id()]
        );
        foreach($rows as $row) {
            yield new ConstantPoint(
                $row['point'],
                new Access\MySqlIdentity($row['user_id'], $this->database),
                new MySqlPoint($row['ID'], $this->database)
            );
        }
    }

    private function rate(int $value) {
        if($this->isReset($value))
            $value = self::NEUTRAL;
        $this->database->query(
            'INSERT INTO bulletpoint_ratings
			(user_id, point, bulletpoint_id)
			VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE point = ?',
            [
                $this->myself->id(),
                $value,
                $this->bulletpoint->id(),
                $value,
            ]
        );
    }

    private function isReset(int $value): bool {
        return $value === self::NEUTRAL
        || (bool)$this->database->fetch(
            'SELECT 1
			FROM bulletpoint_ratings
			WHERE bulletpoint_id = ? AND point = ? AND user_id = ?',
            [$this->bulletpoint->id(), $value, $this->myself->id()]
        );
    }
}