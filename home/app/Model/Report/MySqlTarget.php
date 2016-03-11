<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;

final class MySqlTarget implements Target {
	private $id;
	private $myself;
	private $database;

	public function __construct(
		int $id,
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->id = $id;
		$this->myself = $myself;
		$this->database = $database;
	}

	public function id(): int {
		return $this->id;
	}

	public function complaints(): \Iterator {
		$rows = $this->database->fetchAll(
			'SELECT comment_complaints.ID,
			reason,
			user_id,
			users.role,
			users.username
			FROM comment_complaints
			INNER JOIN users
			ON users.ID = user_id
			WHERE comment_complaints.comment_id = ? AND settled = 0
			ORDER BY reason ASC',
			[$this->id]
		);
		foreach($rows as $row) {
			yield new ConstantComplaint(
				new Access\ConstantIdentity(
					$row['user_id'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole($row['user_id'], $this->database)
					),
					$row['username']
				),
				$this,
				$row['reason'],
				new MySqlComplaint($row['ID'], $this->myself, $this->database)
			);
		}
	}
}