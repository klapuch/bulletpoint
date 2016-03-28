<?php
namespace Bulletpoint\Model\Access;

use Nette;
use Bulletpoint\Model\{
    Storage, Security
};
use Nette\Security\{
    IAuthenticator, Identity, AuthenticationException
};

final class Authenticator implements IAuthenticator {
    private $database;
    private $cipher;

    public function __construct(
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        $this->database = $database;
        $this->cipher = $cipher;
    }

    function authenticate(array $credentials) {
        list($plainUsername, $plainPassword) = $credentials;
        list($id, $password, $role, $username) = $this->database->query(
            'SELECT ID, `password`, role, username
             FROM users
             WHERE username = ?',
            [$plainUsername]
        )->fetch(\PDO::FETCH_NUM);
        if(!$this->exists($id))
            throw new AuthenticationException('Uživatel neexistuje');
        elseif(!$this->activation($id))
            throw new AuthenticationException('Účet není aktivován');
        elseif(!$this->cipher->decrypt($plainPassword, $password))
            throw new AuthenticationException('Nesprávné heslo');
        return new Identity($id, $role, ['username' => $username]);
    }

    private function exists($id): bool {
        return (int)$id !== 0;
    }

    private function activation(int $id): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM verification_codes WHERE user_id = ? AND used = 1',
            [$id]
        );
    }
}