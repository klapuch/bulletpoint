<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
use Bulletpoint\Exception;

final class MySqlBulletpoints implements Bulletpoints {
	private $myself;
	private $database;

	public function __construct(
		Access\Identity $myself,
		Storage\Database $database
	) {
		$this->myself = $myself;
		$this->database = $database;
	}

	public function byIdentity(Access\Identity $identity): \Iterator {
		return $this->iterate('users.ID = ?', [$identity->id()]);
	}

	public function byDocument(Document $document): \Iterator {
		return $this->iterate('document_id = ?', [$document->id()]);
	}

	public function add(
		Document $document,
		string $content,
		InformationSource $source
	) {
		if($this->exists($document, $content))
			throw new Exception\DuplicateException('Bulletpoint již existuje');
		$this->database->query(
			'INSERT INTO bulletpoints
			(user_id, content, information_source_id, document_id)
			VALUES (?, ?, ?, ?)',
			[
				$this->myself->id(),
				$content,
				$source->id(),
				$document->id(),
			]
		);
	}

	private function exists(Document $document, string $content): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM bulletpoints WHERE document_id = ? AND content = ?',
			[$document->id(), $content]
		);
	}

	private function iterate(string $where, array $parameters): \Iterator {
		$rows = $this->database->fetchAll(
			"SELECT users.ID, users.role, users.username,
			information_sources.ID AS source_id,
			information_sources.place,
			information_sources.`year`,
			information_sources.author,
			bulletpoints.ID AS bulletpoint_id,
			bulletpoints.created_at,
			bulletpoints.content
			FROM bulletpoints
			INNER JOIN information_sources
			ON information_sources.ID = bulletpoints.information_source_id
			INNER JOIN users
			ON users.ID = bulletpoints.user_id
			WHERE $where
			ORDER BY bulletpoints.created_at DESC, bulletpoints.document_id",
			$parameters
		);
		foreach($rows as $row) {
			yield new ConstantBulletpoint(
				new Access\ConstantIdentity(
					$row['ID'],
					new Access\ConstantRole(
						$row['role'],
						new Access\MySqlRole($row['ID'], $this->database)
					),
					$row['username']
				),
				$row['content'],
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
				new MySqlBulletpoint($row['bulletpoint_id'], $this->database)
			);
		}
	}
}