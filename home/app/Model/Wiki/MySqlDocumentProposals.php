<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core;
use Bulletpoint\Core\Storage;
use Bulletpoint\Model\{Access, Text};
use Bulletpoint\Exception;

final class MySqlDocumentProposals implements DocumentProposals {
	private $myself;
	private $database;

	public function __construct(
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->myself = $myself;
		$this->database = $database;
	}

	public function iterate(): \Iterator {
		$rows = $this->database->fetchAll(
			'SELECT document_proposals.ID AS proposal_id,
			document_proposals.description,
			document_proposals.title,
			document_proposals.author AS proposal_author,
			users.role,
			users.username,
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			document_proposals.proposed_at,
			information_sources.author
			FROM document_proposals
			INNER JOIN users
			ON users.ID = document_proposals.author
			INNER JOIN information_sources
			ON information_sources.ID = document_proposals.information_source_id
			WHERE decision = "0"
			ORDER BY proposed_at DESC'
		);
		foreach($rows as $row) {
			yield new ConstantDocumentProposal(
				new Access\ConstantIdentity(
					$row['proposal_author'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole(
							$row['proposal_author'],
							$this->database
						)
					),
					$row['username']
				),
				new \DateTime($row['proposed_at']),
				new ConstantInformationSource(
					$row['place'],
					$row['year'],
					$row['author'],
					new MySqlInformationSource(
						$row['source_id'],
						$this->database
					)
				),
				$row['title'],
				$row['description'],
				new MySqlDocumentProposal(
					$row['proposal_id'],
					$this->myself,
					$this->database
				)
			);
		}
	}

	public function propose(
		string $title,
		string $description,
		InformationSource $source
	) {
		if($this->isDuplicate($title))
			throw new Exception\DuplicateException('Dokument již existuje');
		$this->database->query(
			'INSERT INTO document_proposals
			(title, description, author, information_source_id)
			VALUES (?, ?, ?, ?)',
			[
				$title,
				$description,
				$this->myself->id(),
				$source->id(),
			]
		);
	}

	private function isDuplicate(string $title): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM documents WHERE title = ?',
			[$title]
		);
	}
}