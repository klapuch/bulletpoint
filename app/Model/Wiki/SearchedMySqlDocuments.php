<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};

final class SearchedMySqlDocuments implements Documents, \Countable {
    const MASK = '%s*';
    private $keyword;
    private $database;
    private $origin;

    public function __construct(
        string $keyword,
        Storage\Database $database,
        Documents $origin
    ) {
        $this->keyword = $keyword;
        $this->database = $database;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID,
			title,
			user_id,
			information_source_id,
			created_at,
			CASE 
         		WHEN CHAR_LENGTH(description) >= 100 THEN
         			CONCAT(SUBSTRING(description, 1, 100), "...")
         		ELSE
         			description
       		END AS description
			FROM documents
			WHERE MATCH(title) AGAINST(? IN BOOLEAN MODE)
			ORDER BY MATCH(title) AGAINST(? IN BOOLEAN MODE) DESC',
            array_fill(0, 2, sprintf(self::MASK, $this->keyword))
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

    public function count() {
        return $this->database->fetchColumn(
            'SELECT COUNT(*)
			FROM documents
			WHERE MATCH(title) AGAINST(? IN BOOLEAN MODE)',
            [sprintf(self::MASK, $this->keyword)]
        );
    }

    public function add(
        string $title,
        string $description,
        InformationSource $source
    ): Document {
        return $this->origin->add($title, $description, $source);
    }
}