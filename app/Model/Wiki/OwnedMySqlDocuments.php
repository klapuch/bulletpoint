<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class OwnedMySqlDocuments implements Documents {
    private $owner;
    private $database;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database
    ) {
        $this->owner = $owner;
        $this->database = $database;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID, created_at, description, title, information_source_id
			FROM documents
			WHERE user_id = ?
			ORDER BY documents.created_at DESC',
            [$this->owner->id()]
        );
        foreach($rows as $row) {
            yield new ConstantDocument(
                $row['title'],
                $row['description'],
                $this->owner,
                new \DateTime($row['created_at']),
                new MySqlInformationSource(
                    $row['information_source_id'],
                    $this->database
                ),
                new MySqlDocument($row['ID'], $this->database)
            );
        }
    }

    public function add(
        string $title,
        string $description,
        InformationSource $source
    ): Document {
        try {
            $this->database->query(
                'INSERT INTO documents
                (user_id, information_source_id, description, title)
                VALUES (?, ?, ?, ?)',
                [
                    $this->owner->id(),
                    $source->id(),
                    $description,
                    $title,
                ]
            );
            return new MySqlDocument(
                $this->database->fetchColumn('SELECT LAST_INSERT_ID()'),
                $this->database
            );
        } catch(\PDOException $ex) {
            if($ex->getCode() === Storage\Database::INTEGRITY_CONSTRAINT)
                throw new Exception\DuplicateException('Titulek již existuje');
            throw $ex;
        }
    }

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM documents WHERE user_id = ?',
            [$this->owner->id()]
        );
    }
}