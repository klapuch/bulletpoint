<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;
use Bulletpoint\Model\Storage;

final class CommentExistenceRule implements Rule {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function isSatisfied($input) {
        if(!$this->exists($input))
            throw new Exception\ExistenceException('Komentář neexistuje');
    }

    private function exists($input): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM comments WHERE ID = ?',
            [$input]
        );
    }
}