<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;

final class MySqlSin implements Sin {
	private $id;
	private $database;

	public function __construct(int $id, Storage\Database $database) {
		$this->id = $id;
		$this->database = $database;
	}

	public function sinner(): Access\Identity {
		return new Access\MySqlIdentity(
			$this->database->fetchColumn(
				'SELECT user_id FROM banned_users WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function id(): int {
		return $this->id;
	}

	public function reason(): string {
		return $this->database->fetchColumn(
			'SELECT reason FROM banned_users WHERE ID = ?',
			[$this->id]
		);
	}

	public function expiration(): \DateTime {
		return new \DateTime(
				$this->database->fetchColumn(
				'SELECT expiration FROM banned_users WHERE ID = ?',
				[$this->id]
			)
		);
	}

	public function expired(): bool {
		return $this->expiration() <= new \DateTime;
	}

	public function forgive() {
		$this->database->query(
			'UPDATE banned_users SET canceled = 1 WHERE ID = ?',
			[$this->id]
		);
	}
}