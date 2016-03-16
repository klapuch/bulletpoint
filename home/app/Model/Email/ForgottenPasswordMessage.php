<?php
namespace Bulletpoint\Model\Email;

use Bulletpoint\Core\Storage;

final class ForgottenPasswordMessage implements Message {
	private $recipient;
	private $database;

	public function __construct(string $recipient, Storage\Database $database) {
		$this->recipient = $recipient;
		$this->database = $database;
	}

	public function sender(): string {
		return 'bulletpoint <zapomenute-heslo@bulletpoint.cz>';
	}

	public function recipient(): string {
		return $this->recipient;
	}

	public function subject(): string {
		return 'Zapomenuté heslo na bulletpoint';
	}

	public function content(): string {
		return sprintf(
			$this->database->fetchColumn(
				'SELECT SQL_CACHE message
				FROM message_templates
				WHERE designation = "forgotten-password"'
			),
			...$this->database->query(
				'SELECT reminder, reminder
				FROM forgotten_passwords
				WHERE user_id = (SELECT ID FROM users WHERE email = ?)
				ORDER BY reminded_at DESC
				LIMIT 1',
				[$this->recipient]
			)->fetch(\PDO::FETCH_NUM)
		);
	}
}