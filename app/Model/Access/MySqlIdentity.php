<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;

final class MySqlIdentity implements Identity {
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function id(): int {
        return $this->id;
    }

    public function role(): Role {
        return new MySqlRole($this->id, $this->database);
    }

    public function username(): string {
        return $this->database->fetchColumn(
            'SELECT username FROM users WHERE ID = ?',
            [$this->id]
        );
    }
}