<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlDocumentSlugs implements Slugs {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function add(int $origin, string $slug): Slug {
        try {
            $this->database->query(
                'INSERT INTO document_slugs (slug, origin) VALUES (?, ?)',
                [$slug, $origin]
            );
            return new MySqlDocumentSlug($slug, $this->database);
        } catch(\PDOException $ex) {
            if($ex->getCode() === Storage\Database::INTEGRITY_CONSTRAINT) {
                throw new Exception\DuplicateException(
                    sprintf(
                        'Slug "%s" již existuje',
                        $slug
                    )
                );
            }
            throw $ex;
        }
    }
}