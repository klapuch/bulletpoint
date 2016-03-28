<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class CategorizedMySqlBulletpoints extends Bulletpoints {
    private $myself;
    private $document;

    public function __construct(
        Access\Identity $myself,
        Storage\Database $database,
        Document $document
    ) {
        parent::__construct($database);
        $this->myself = $myself;
        $this->document = $document;
    }

    public function iterate(): \Iterator {
        return $this->iterateBy('document_id = ?', [$this->document->id()]);
    }

    public function add(string $content, InformationSource $source) {
        try {
            $this->database->query(
                'INSERT INTO bulletpoints
			    (user_id, content, information_source_id, document_id)
			    VALUES (?, ?, ?, ?)',
                [
                    $this->myself->id(),
                    $content,
                    $source->id(),
                    $this->document->id(),
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

}