<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;
use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class MySqlBulletpointProposal implements BulletpointProposal {
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
				'SELECT author FROM bulletpoint_proposals WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function content(): string {
		return $this->database->fetchColumn(
			'SELECT content FROM bulletpoint_proposals WHERE ID = ?',
			[$this->id]
		);
	}

	public function source(): InformationSource {
		return new MySqlInformationSource(
			$this->database->fetchColumn(
				'SELECT information_source_id 
				FROM bulletpoint_proposals
				WHERE ID = ?', [$this->id]
			),
			$this->database
		);
	}

	public function id(): int {
		return $this->id;
	}

	public function date(): \Datetime {
		return new \Datetime(
			$this->database->fetchColumn(
				'SELECT proposed_at FROM bulletpoint_proposals WHERE ID = ?',
				[$this->id]
			)
		);
	}

	public function document(): Document {
		return new MySqlDocument(
			$this->database->fetchColumn(
				'SELECT document_id FROM bulletpoint_proposals WHERE ID = ?',
				[$this->id]
			),
			$this->database
		);
	}

	public function edit(string $content) {
		if($this->exists($content))
			throw new Exception\DuplicateException('Bulletpoint již existuje');
		$this->database->query(
			'UPDATE bulletpoint_proposals SET content = ? WHERE ID = ?',
			[$content, $this->id]
		);
	}

	public function accept(): Bulletpoint {
		if($this->exists($this->content()))
			throw new Exception\DuplicateException('Bulletpoint již existuje');
		$this->database->query(
			'INSERT INTO bulletpoints
			(information_source_id, user_id, content, document_id)
			SELECT ?, author, content, document_id
			FROM bulletpoint_proposals
			WHERE ID = ?',
			[$this->source()->id(), $this->id]
		);
		$bulletpointId = $this->database->fetchColumn('SELECT LAST_INSERT_ID()');
		$this->database->query(
			'UPDATE bulletpoint_proposals
			SET decision = "+1",
			arbiter = ?,
			decided_at = NOW()
			WHERE ID = ?',
			[$this->myself->id(), $this->id]
		);
		return new MySqlBulletpoint($bulletpointId, $this->database);
	}

	public function reject(string $reason = null) {
		$this->database->query(
			'UPDATE bulletpoint_proposals
			SET decision = "-1",
			arbiter = ?,
			arbiter_comment = ?,
			decided_at = NOW()
			WHERE ID = ?',
			[$this->myself->id(), $reason, $this->id]
		);
	}

	private function exists(string $content): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM bulletpoints WHERE document_id = ? AND content = ?',
			[$this->document()->id(), $content]
		);
	}
}