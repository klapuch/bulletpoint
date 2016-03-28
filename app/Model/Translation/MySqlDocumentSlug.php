<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlDocumentSlug implements Slug {
    private $identificator;
    private $database;

    public function __construct($identificator, Storage\Database $database) {
        $this->identificator = $identificator;
        $this->database = $database;
    }

    public function origin(): int {
        if(is_int($this->identificator))
            return $this->identificator;
        return $this->database->fetchColumn(
            'SELECT origin FROM document_slugs WHERE slug = ?',
            [$this->identificator]
        );
    }

    public function rename(string $newSlug): Slug {
        try {
            $this->database->query(
                'UPDATE document_slugs SET slug = ? WHERE origin = ?',
                [$newSlug, $this->origin()]
            );
            return new self($newSlug, $this->database);
        } catch(\PDOException $ex) {
            if($ex->getCode() === Storage\Database::INTEGRITY_CONSTRAINT) {
                throw new Exception\DuplicateException(
                    sprintf(
                        'Slug "%s" již existuje',
                        $newSlug
                    )
                );
            }
            throw $ex;
        }
    }

    public function __toString() {
        if(is_string($this->identificator))
            return $this->identificator;
        return $this->database->fetchColumn(
            'SELECT slug FROM document_slugs WHERE origin = ?',
            [$this->identificator]
        );
    }
}