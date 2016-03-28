<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class EmailExistenceRule implements Rule {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function isSatisfied($email) {
        if(!$this->exists($email))
            throw new Exception\ExistenceException('Email neexistuje');
    }

    private function exists(string $email): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM users WHERE email = ?',
            [$email]
        );
    }
}