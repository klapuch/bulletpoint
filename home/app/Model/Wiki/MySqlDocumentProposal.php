<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\{Access, Text};
use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class MySqlDocumentProposal implements DocumentProposal {
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
				'SELECT author FROM document_proposals WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function description(): string {
		return $this->database->fetchColumn(
			'SELECT description FROM document_proposals WHERE ID = ?',
			[$this->id]
		);
	}

	public function title(): string {
		return $this->database->fetchColumn(
			'SELECT title FROM document_proposals WHERE ID = ?',
			[$this->id]
		);
	}

	public function source(): InformationSource {
		return new MySqlInformationSource(
			$this->database->fetchColumn(
				'SELECT information_source_id 
				FROM document_proposals
				WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function date(): \Datetime {
		return new \Datetime(
			$this->database->fetchColumn(
				'SELECT proposed_at FROM document_proposals WHERE ID = ?',
				[$this->id]
			)
		);
	}

	public function id(): int {
		return $this->id;
	}

	public function edit(string $title, string $description) {
		if($this->exists($title))
			throw new Exception\DuplicateException('Titulek již existuje');
		$this->database->query(
			'UPDATE document_proposals
			SET title = ?, description = ?
			WHERE ID = ?',
			[$title, $description, $this->id]
		);
	}

	public function accept(): Document {
		if($this->exists($this->title()))
			throw new Exception\DuplicateException('Titulek již existuje');
		$this->database->query(
			'INSERT INTO documents
			(information_source_id, user_id, title, description)
			SELECT ?, author, title, description
			FROM document_proposals
			WHERE ID = ?',
			[$this->source()->id(), $this->id]
		);
		$documentId = $this->database->fetchColumn('SELECT LAST_INSERT_ID()');
		$this->database->query(
			'UPDATE document_proposals
			SET decision = "+1",
			arbiter = ?,
			decided_at = NOW()
			WHERE ID = ?',
			[$this->myself->id(), $this->id]
		);
		return new MySqlDocument($documentId, $this->database);
	}

	public function reject(string $reason = null) {
		$this->database->query(
			'UPDATE document_proposals
			SET decision = "-1",
			arbiter = ?,
			arbiter_comment = ?,
			decided_at = NOW()
			WHERE ID = ?',
			[$this->myself->id(), $reason, $this->id]
		);
	}

	private function exists(string $title): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM documents WHERE title = ?',
			[$title]
		);
	}
}