<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class MySqlBulletpoint implements Bulletpoint {
    private $id;
    private $database;

    public function __construct(int $id, Storage\Database $database) {
        $this->id = $id;
        $this->database = $database;
    }

    public function author(): Access\Identity {
        return new Access\MySqlIdentity(
            $this->database->fetchColumn(
                'SELECT user_id FROM bulletpoints WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function content(): string {
        return $this->database->fetchColumn(
            'SELECT content FROM bulletpoints WHERE ID = ?',
            [$this->id]
        );
    }

    public function source(): InformationSource {
        return new MySqlInformationSource(
            $this->database->fetchColumn(
                'SELECT information_source_id
				FROM bulletpoints
				WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function id(): int {
        return $this->id;
    }

    public function date(): \DateTimeImmutable {
        return new \DateTimeImmutable(
            $this->database->fetchColumn(
                'SELECT created_at FROM bulletpoints WHERE ID = ?',
                [$this->id]
            )
        );
    }

    public function document(): Document {
        return new MySqlDocument(
            $this->database->fetchColumn(
                'SELECT document_id FROM bulletpoints WHERE ID = ?',
                [$this->id]
            ),
            $this->database
        );
    }

    public function edit(string $content) {
        try {
            $this->database->query(
                'UPDATE bulletpoints SET content = ? WHERE ID = ?',
                [$content, $this->id]
            );
        } catch(\PDOException $ex) {
            if($ex->getCode() === Storage\Database::INTEGRITY_CONSTRAINT) {
                throw new Exception\DuplicateException(
                    'Bulletpoint již existuje'
                );
            }
            throw $ex;
        }
    }
}