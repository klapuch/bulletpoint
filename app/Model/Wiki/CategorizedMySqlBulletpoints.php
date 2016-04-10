<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class CategorizedMySqlBulletpoints implements Bulletpoints {
    private $database;
    private $document;
    private $origin;

    public function __construct(
        Storage\Database $database,
        Document $document,
        Bulletpoints $origin
    ) {
        $this->database = $database;
        $this->document = $document;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT 
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			information_sources.author,
			bulletpoints.ID AS bulletpoint_id,
			bulletpoints.user_id,
			bulletpoints.created_at,
			bulletpoints.content
			FROM bulletpoints
			LEFT JOIN information_sources
			ON information_sources.ID = bulletpoints.information_source_id
			WHERE document_id = ?
			ORDER BY bulletpoints.created_at DESC',
            [$this->document->id()]
        );
        foreach($rows as $row) {
            yield new ConstantBulletpoint(
                new Access\MySqlIdentity($row['user_id'], $this->database),
                $row['content'],
                new \DateTime($row['created_at']),
                new ConstantInformationSource(
                    $row['place'],
                    $row['year'],
                    $row['author'],
                    new MySqlInformationSource(
                        $row['source_id'],
                        $this->database
                    )
                ),
                new MySqlBulletpoint($row['bulletpoint_id'], $this->database)
            );
        }
    }

    public function add(
        string $content,
        Document $document,
        InformationSource $source
    ) {
        return $this->origin->add($content, $document, $source);
    }

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM bulletpoints WHERE document_id = ?',
            [$this->document->id()]
        );
    }
}