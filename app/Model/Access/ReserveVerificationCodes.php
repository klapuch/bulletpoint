<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class ReserveVerificationCodes implements VerificationCodes {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function generate(string $email): VerificationCode {
        $code = $this->database->fetchColumn(
            'SELECT code
			FROM verification_codes
			WHERE user_id = (SELECT ID FROM users WHERE email = ?)
			AND used = 0',
            [$email]
        );
        if(strlen($code))
            return new MySqlVerificationCode($code, $this->database);
        throw new Exception\ExistenceException(
            'Ověřovací kód již byl použit'
        );
    }
}