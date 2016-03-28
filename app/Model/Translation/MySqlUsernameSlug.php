<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlUsernameSlug implements Slug {
    private $username;
    private $database;

    public function __construct(string $username, Storage\Database $database) {
        $this->username = $username;
        $this->database = $database;
    }

    public function origin(): int {
        return $this->database->fetchColumn(
            'SELECT ID FROM users WHERE username = ?',
            [$this->username]
        );
    }

    public function rename(string $newUsername): Slug {
        try {
            $this->database->query(
                'UPDATE users SET username = ? WHERE username = ?',
                [$newUsername, $this->username]
            );
            return new self($newUsername, $this->database);
        } catch(\PDOException $ex) {
            if($ex->getCode() === Storage\Database::INTEGRITY_CONSTRAINT) {
                throw new Exception\DuplicateException(
                    sprintf(
                        'Přezdívka "%s" již existuje',
                        $newUsername
                    )
                );
            }
            throw $ex;
        }
    }

    public function __toString() {
        return $this->username;
    }
}