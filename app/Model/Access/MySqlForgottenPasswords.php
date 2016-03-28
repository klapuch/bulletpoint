<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;

final class MySqlForgottenPasswords implements ForgottenPasswords {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function remind(string $email) {
        $reminder = bin2hex(random_bytes(50)) . ':' . sha1($email);
        $this->database->query(
            'INSERT INTO forgotten_passwords (user_id, reminder)
			VALUES ((SELECT ID FROM users WHERE email = ?), ?)',
            [$email, $reminder]
        );
    }
}