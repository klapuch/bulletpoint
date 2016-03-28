<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlVerificationCodes implements VerificationCodes {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function generate(string $email): VerificationCode {
        $code = bin2hex(random_bytes(25)) . ':' . sha1($email);
        $this->database->query(
            'INSERT INTO verification_codes (user_id, code)
			VALUES ((SELECT ID FROM users WHERE email = ?), ?)',
            [$email, $code]
        );
        return new MySqlVerificationCode($code, $this->database);
    }
}