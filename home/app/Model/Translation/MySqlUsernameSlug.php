<?php
namespace Bulletpoint\Model\Translation;

use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class MySqlUsernameSlug implements Slug {
	private $username;
	private $database;

	public function __construct(string $username, Storage\Database $database) {
		$this->username = $username;
		$this->database = $database;
	}

	public function origin(): int {
		return $this->database->fetchColumn(
			'SELECT ID FROM users WHERE username = ?',
			[$this->username]
		);
	}

	public function rename(string $newUsername): Slug {
		if($this->exists($newUsername)) {
			throw new Exception\DuplicateException(
				sprintf(
					'Přezdívka "%s" již existuje',
					$newUsername
				)
			);
		}
		$this->database->query(
			'UPDATE users SET username = ? WHERE username = ?',
			[$newUsername, $this->username]
		);
		return new self($newUsername, $this->database);
	}

	public function __toString() {
		return $this->username;
	}

	private function exists(string $newSlug): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM users WHERE username = ? AND ID != ?',
			[$newSlug, $this->origin()]
		);
	}
}