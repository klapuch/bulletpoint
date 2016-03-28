<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\{
    Storage, Access
};

final class MySqlPunishment implements Punishment {
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function sinner(): Access\Identity {
        return new Access\MySqlIdentity(
            $this->database->fetchColumn(
                'SELECT sinner_id FROM punishments WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function id(): int {
        return $this->id;
    }

    public function reason(): string {
        return $this->database->fetchColumn(
            'SELECT reason FROM punishments WHERE ID = ?',
            [$this->id]
        );
    }

    public function expiration(): \DateTime {
        return new \DateTime(
            $this->database->fetchColumn(
                'SELECT expiration FROM punishments WHERE ID = ?',
                [$this->id]
            )
        );
    }

    public function expired(): bool {
        return $this->expiration() <= new \DateTime || $this->forgiven();
    }

    public function forgive() {
        $this->database->query(
            'UPDATE punishments SET forgiven = 1 WHERE ID = ?',
            [$this->id]
        );
    }

    private function forgiven(): bool {
        return (bool)$this->database->fetchColumn(
            'SELECT forgiven FROM punishments WHERE ID = ?',
            [$this->id]
        );
    }
}