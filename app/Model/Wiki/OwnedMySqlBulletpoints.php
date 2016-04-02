<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

final class OwnedMySqlBulletpoints implements Bulletpoints {
    private $owner;
    private $database;
    private $origin;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database,
        Bulletpoints $origin
    ) {
        $this->database = $database;
        $this->owner = $owner;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
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
        foreach($rows as $row) {
            yield new ConstantBulletpoint(
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
        }
    }

    public function add(string $content, InformationSource $source) {
        $this->origin->add($content, $source);
    }
}