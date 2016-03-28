<?php
namespace Bulletpoint\Model\Email;

use Bulletpoint\Model\Storage;

final class ActivationMessage implements Message {
    private $recipient;
    private $database;

    public function __construct(string $recipient, Storage\Database $database) {
        $this->recipient = $recipient;
        $this->database = $database;
    }

    public function sender(): string {
        return 'bulletpoint <aktivace@bulletpoint.cz>';
    }

    public function recipient(): string {
        return $this->recipient;
    }

    public function subject(): string {
        return 'Aktivace účtu na bulletpoint';
    }

    public function content(): string {
        return sprintf(
            $this->database->fetchColumn(
                'SELECT SQL_CACHE message
				FROM message_templates
				WHERE designation = "activation"'
            ),
            ...$this->database->query(
                'SELECT code, code
				FROM verification_codes
				WHERE user_id = (SELECT ID FROM users WHERE email = ?)',
                [$this->recipient]
            )->fetch(\PDO::FETCH_NUM)
        );
    }
}