<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Storage;

final class Fulltext implements SearchEngine {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function matches(string $keyword): array {
        if(!strlen($keyword))
            return [0 => []];
        $results = $this->database->fetchAll(
            'SELECT SQL_CALC_FOUND_ROWS
			documents.ID,
			title,
			created_at,
			document_slugs.slug,
			CASE 
         		WHEN CHAR_LENGTH(description) >= 100 THEN
         			CONCAT(SUBSTRING(description, 1, 100), "...")
         		ELSE
         			description
       		END AS description 
			FROM documents
			INNER JOIN document_slugs
			ON document_slugs.origin = documents.ID
			WHERE MATCH(title) AGAINST(? IN BOOLEAN MODE)
			ORDER BY MATCH(title) AGAINST(? IN BOOLEAN MODE) DESC',
            array_fill(0, 2, $keyword . '*')
        );
        return [
            $this->database->fetchColumn('SELECT FOUND_ROWS()') => $results,
        ];
    }
}