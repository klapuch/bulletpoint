<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\{
    Storage, Security
};

final class MySqlForgottenPasswords implements ForgottenPasswords {
    private $database;
    private $cipher;

    public function __construct(
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function remind(string $email): RemindedPassword {
        $reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
        $this->database->query(
            'INSERT INTO forgotten_passwords (user_id, reminder)
			VALUES ((SELECT ID FROM users WHERE email = ?), ?)',
            [$email, $reminder]
        );
        return new MySqlRemindedPassword(
            $reminder,
            $this->database,
            $this->cipher
        );
    }
}