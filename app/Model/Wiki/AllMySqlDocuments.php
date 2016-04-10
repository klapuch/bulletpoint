<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

final class AllMySqlDocuments implements Documents {
    private $database;
    private $origin;

    public function __construct(Storage\Database $database, Documents $origin) {
        $this->database = $database;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID,
              created_at,
              description,
              title,
              information_source_id,
              user_id
			  FROM documents
			  ORDER BY created_at DESC'
        );
        foreach($rows as $row) {
            yield new ConstantDocument(
                $row['title'],
                $row['description'],
                new Access\MySqlIdentity($row['user_id'], $this->database),
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
        return $this->origin->add($title, $description, $source);
    }

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM documents'
        );
    }
}