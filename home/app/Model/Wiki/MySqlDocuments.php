<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
use Bulletpoint\Exception;

final class MySqlDocuments implements Documents {
	private $myself;
	private $database;

	public function __construct(
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->myself = $myself;
		$this->database = $database;
	}

	public function iterate(Access\Identity $identity): \Iterator {
		$rows = $this->database->fetchAll(
			'SELECT users.ID AS user_id,
			users.role,
			users.username,
			documents.ID,
			documents.created_at,
			documents.description,
			documents.title,
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			information_sources.author
			FROM documents
			INNER JOIN users
			ON users.ID = documents.user_id
			INNER JOIN information_sources
			ON documents.information_source_id = information_sources.ID
			WHERE user_id = ?
			ORDER BY documents.created_at DESC',
			[$identity->id()]
		);
		foreach($rows as $row) {
			yield new ConstantDocument(
				$row['title'],
				$row['description'],
				new Access\ConstantIdentity(
					$row['user_id'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole($row['user_id'], $this->database)
					),
					$row['username']
				),
				new \Datetime($row['created_at']),
				new ConstantInformationSource(
					$row['place'],
					$row['year'],
					$row['author'],
					new MySqlInformationSource(
						$row['source_id'],
						$this->database
					)
				),
				new MySqlDocument($row['ID'], $this->database)
			);
		}
	}

	public function add(
		string $title,
		string $description,
		InformationSource $source
	) {
		if($this->isDuplicate($title))
			throw new Exception\DuplicateException('Titulek již existuje');
		$this->database->query(
			'INSERT INTO documents
			(user_id, information_source_id, description, title)
			VALUES (?, ?, ?, ?)',
			[
				$this->myself->id(),
				$source->id(),
				$description,
				$title,
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