<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Access, Storage
};

final class MySqlPoint implements Point {
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function id(): int {
        return $this->id;
    }

    public function value(): int {
        return $this->database->fetchColumn(
            'SELECT point FROM bulletpoint_ratings WHERE ID = ?',
            [$this->id()]
        );
    }

    public function voter(): Access\Identity {
        return new Access\MySqlIdentity(
            $this->database->fetchColumn(
                'SELECT user_id FROM bulletpoint_ratings WHERE ID = ?',
                [$this->id()]
            ),
            $this->database
        );
    }
}