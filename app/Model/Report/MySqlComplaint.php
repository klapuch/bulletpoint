<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\{
    Access, Storage
};

final class MySqlComplaint implements Complaint {
    private $id;
    private $myself;
    private $database;

    public function __construct(
        int $id,
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->id = $id;
        $this->myself = $myself;
        $this->database = $database;
    }

    public function id(): int {
        return $this->id;
    }

    public function critic(): Access\Identity {
        $id = $this->database->fetchColumn(
            'SELECT user_id FROM comment_complaints WHERE ID = ?',
            [$this->id]
        );
        return new Access\MySqlIdentity($id, $this->database);
    }

    public function target(): Target {
        return new Target(
            $this->database->fetchColumn(
                'SELECT comment_id FROM comment_complaints WHERE ID = ?',
                [$this->id]
            )
        );
    }

    public function reason(): string {
        return $this->database->fetchColumn(
            'SELECT reason FROM comment_complaints WHERE ID = ?',
            [$this->id]
        );
    }

    public function settle() {
        $this->database->query(
            'UPDATE comment_complaints SET settled = 1 WHERE ID = ?',
            [$this->id]
        );
    }
}