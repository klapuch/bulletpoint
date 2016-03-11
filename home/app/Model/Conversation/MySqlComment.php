<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
use Bulletpoint\Exception;

final class MySqlComment implements Comment {
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

	public function author(): Access\Identity {
		return new Access\MySqlIdentity(
			$this->database->fetchColumn(
				'SELECT user_id FROM comments WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function content(): string {
		return $this->database->fetchColumn(
			'SELECT content FROM comments WHERE ID = ?',
			[$this->id]
		);
	}

	public function date(): \Datetime {
		return new \Datetime(
			$this->database->fetchColumn(
				'SELECT posted_at FROM comments WHERE ID = ?',
				[$this->id]
			)
		);
	}

	public function id(): int {
		return $this->id;
	}

	public function edit(string $content) {
		if($this->myself->id() !== $this->author()->id()) {
			throw new Exception\AccessDeniedException(
				'Tento komentář jsi nenapsal'
			);
		} elseif(!$this->visible()) {
			throw new Exception\AccessDeniedException(
				'Tento komentář již nemůže být upravován'
			);
		}
		$this->database->query(
			'UPDATE comments
			SET content = ?
			WHERE ID = ?',
			[$content, $this->id]
		);
	}

	public function visible(): bool {
		return (bool)$this->database->fetchColumn(
			'SELECT visible FROM comments WHERE ID = ?',
			[$this->id]
		);
	}

	public function erase() {
		if(!$this->visible())
			throw new \LogicException('Komentář je již smazán');
		$this->database->query(
			'UPDATE comments SET visible = 0 WHERE ID = ?',
			[$this->id]
		);
	}
}