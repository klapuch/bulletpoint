<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Storage, Access
};

final class OwnedMySqlBulletpoints implements Bulletpoints {
    private $owner;
    private $database;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database
    ) {
        $this->database = $database;
        $this->owner = $owner;
    }

    public function iterate(): array {
        $rows = $this->database->fetchAll(
            'SELECT bulletpoints.ID,
            bulletpoints.user_id,
            bulletpoints.created_at,
            bulletpoints.content,
            bulletpoints.information_source_id,
            documents.title,
            documents.description,
            documents.user_id AS document_author,
            documents.created_at AS document_date,
            documents.information_source_id AS document_source,
            documents.ID AS document_id
			FROM bulletpoints
			LEFT JOIN documents
			ON bulletpoints.document_id = documents.ID
			WHERE bulletpoints.user_id = ?
			ORDER BY bulletpoints.created_at DESC',
            [$this->owner->id()]
        );
        return (array)array_reduce(
            $rows,
            function($previous, $row) {
                $previous[] = new ConstantBulletpoint(
                    $this->owner,
                    $row['content'],
                    new \DateTime($row['created_at']),
                    new MySqlInformationSource(
                        $row['information_source_id'],
                        $this->database
                    ),
                    new MySqlBulletpoint($row['ID'], $this->database),
                    new ConstantDocument(
                        $row['title'],
                        $row['description'],
                        new Access\MySqlIdentity(
                            $row['document_author'],
                            $this->database
                        ),
                        new \DateTime($row['document_date']),
                        new MySqlInformationSource(
                            $row['document_source'],
                            $this->database
                        ),
                        new MySqlDocument($row['document_id'], $this->database)
                    )
                );
                return $previous;
            }
        );
    }

    public function add(
        string $content,
        Document $document,
        InformationSource $source
    ) {
        try {
            $this->database->query(
                'INSERT INTO bulletpoints
			    (user_id, content, information_source_id, document_id)
			    VALUES (?, ?, ?, ?)',
                [
                    $this->owner->id(),
                    $content,
                    $source->id(),
                    $document->id(),
                ]
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

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM bulletpoints WHERE user_id = ?',
            [$this->owner->id()]
        );
    }
}