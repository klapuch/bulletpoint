<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

final class MySqlDocument implements Document {
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function author(): Access\Identity {
        return new Access\MySqlIdentity(
            $this->database->fetchColumn(
                'SELECT user_id FROM documents WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function description(): string {
        return $this->database->fetchColumn(
            'SELECT description FROM documents WHERE ID = ?',
            [$this->id]
        );
    }

    public function title(): string {
        return $this->database->fetchColumn(
            'SELECT title FROM documents WHERE ID = ?',
            [$this->id]
        );
    }

    public function date(): \DateTime {
        return new \DateTime(
            $this->database->fetchColumn(
                'SELECT created_at FROM documents WHERE ID = ?',
                [$this->id]
            )
        );
    }

    public function source(): InformationSource {
        return new MySqlInformationSource(
            $this->database->fetchColumn(
                'SELECT information_source_id FROM documents WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function id(): int {
        return $this->id;
    }

    public function edit(string $title, string $description) {
        $this->database->query(
            'UPDATE documents SET title = ?, description = ? WHERE ID = ?',
            [$title, $description, $this->id]
        );
    }
}