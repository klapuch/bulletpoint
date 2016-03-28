<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Model\{
    Storage, Security, Access
};

final class Account {
    private $myself;
    private $database;
    private $cipher;

    public function __construct(
        Access\Identity $myself,
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        $this->myself = $myself;
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function email(): string {
        return $this->database->fetchColumn(
            'SELECT email FROM users WHERE ID = ?',
            [$this->myself->id()]
        );
    }

    public function changePassword(string $password) {
        $this->database->query(
            'UPDATE users SET `password` = ? WHERE ID = ?',
            [
                $this->cipher->encrypt($password),
                $this->myself->id(),
            ]
        );
    }
}