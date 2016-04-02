<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Model\{
    Access, Storage, Filesystem
};
use Bulletpoint\Exception;

final class MySqlProfile implements Profile {
    private $owner;
    private $database;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database
    ) {
        $this->owner = $owner;
        $this->database = $database;
    }

    public function comments(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(ID) FROM comments WHERE user_id = ?',
            [$this->owner->id()]
        );
    }

    public function bulletpoints(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(ID) FROM bulletpoints WHERE user_id = ?',
            [$this->owner->id()]
        );
    }

    public function documents(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(ID) FROM documents WHERE user_id = ?',
            [$this->owner->id()]
        );
    }

    public function owner(): Access\Identity {
        return $this->owner;
    }
}