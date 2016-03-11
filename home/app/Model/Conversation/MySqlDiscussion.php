<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;

final class MySqlDiscussion implements Discussion {
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

	public function contributions(): \Iterator {
		$rows = $this->database->fetchAll(
			'SELECT users.ID AS user_id,
			users.role,
			users.username,
			comments.posted_at,
			comments.content,
			comments.ID, 
			comments.visible
			FROM comments
			INNER JOIN users
			ON users.ID = comments.user_id
			WHERE comments.document_id = ?
			ORDER BY comments.posted_at DESC',
			[$this->id]
		);
		foreach($rows as $row) {
			yield new ConstantComment(
				new Access\ConstantIdentity(
					$row['user_id'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole($row['user_id'], $this->database)
					), 
					$row['username']
				),
				$row['content'],
				new \Datetime($row['posted_at']),
				$row['visible'],
				new MySqlComment($row['ID'], $this->myself, $this->database)
			);
		}
	}

	public function contribute(string $content) {
		$this->database->query(
			'INSERT INTO comments (user_id, content, document_id)
			VALUES (?, ?, ?)',
			[$this->myself->id(), $content, $this->id]
		);
	}
}