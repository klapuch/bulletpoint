<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;
use Bulletpoint\Model\Storage;

final class UsernameExistenceRule implements Rule {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function isSatisfied($username) {
        if(!$this->exists($username))
            throw new Exception\ExistenceException('Přezdívka neexistuje');
    }

    private function exists(string $username): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM users WHERE username = ?',
            [$username]
        );
    }
}