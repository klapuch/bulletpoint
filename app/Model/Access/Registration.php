<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\{
    Storage, Security, User
};
use Bulletpoint\Exception;

final class Registration {
    private $database;
    private $cipher;

    public function __construct(
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function register(User\Applicant $applicant) {
        $this->database->query(
            'INSERT INTO users (username, email, `password`)
			VALUES (?, ?, ?)',
            [
                $applicant->username(),
                $applicant->email(),
                $this->cipher->encrypt($applicant->password()),
            ]
        );
    }
}