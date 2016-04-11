<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;
use Bulletpoint\Exception;

final class MySqlVerificationCode implements VerificationCode {
    private $code;
    private $database;

    public function __construct(string $code, Storage\Database $database) {
        $this->code = $code;
        $this->database = $database;
    }

    public function use(): VerificationCode {
        if($this->used()) {
            throw new Exception\DuplicateException(
                'Ověřovací kód již byl použit'
            );
        }
        $this->database->query(
            'UPDATE verification_codes
			SET used = 1, used_at = NOW()
			WHERE code = ?',
            [$this->code]
        );
        return $this;
    }

    public function owner(): Identity {
        return new MySqlIdentity(
            $this->database->fetchColumn(
                'SELECT user_id FROM verification_codes WHERE code = ?',
                [$this->code]
            ),
            $this->database
        );
    }

    private function used(): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM verification_codes WHERE code = ? AND used = 1',
            [$this->code]
        );
    }
}