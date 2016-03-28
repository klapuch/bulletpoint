<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlRole implements Role {
    const DEGRADE = -1;
    const PROMOTE = +1;
    private $roles = [
        'member' => 1,
        'administrator' => 2,
        'creator' => 3,
    ];
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function degrade(): Role {
        $lowestRank = current($this->roles);
        if($this->rank() === $lowestRank)
            throw new \UnderflowException('Nižší role neexistuje');
        $this->change(self::DEGRADE);
        return clone $this;
    }

    public function promote(): Role {
        $highestRank = end($this->roles);
        reset($this->roles);
        if($this->rank() === $highestRank)
            throw new \OverflowException('Vyšší role neexistuje');
        $this->change(self::PROMOTE);
        return clone $this;
    }

    public function __toString() {
        return $this->database->fetchColumn(
            'SELECT role FROM users WHERE ID = ?',
            [$this->id]
        ) ?: self::DEFAULT_ROLE;
    }

    public function rank(): int {
        return $this->roles[(string)$this] ?? self::DEFAULT_RANK;
    }

    private function change(int $rank) {
        $this->database->query(
            'UPDATE users SET role = ? WHERE ID = ?',
            [
                array_flip($this->roles)[$this->rank() + $rank],
                $this->id,
            ]
        );
    }
}