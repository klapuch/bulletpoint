<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
use Bulletpoint\Exception;

final class CategorizedMySqlBulletpoints extends Bulletpoints {
	private $myself;
	private $document;

	public function __construct(
		Access\Identity $myself,
		Storage\Database $database,
		Document $document
	) {
		parent::__construct($database);
		$this->myself = $myself;
		$this->document = $document;
	}

	public function iterate(): \Iterator {
		return $this->iterateBy('document_id = ?', [$this->document->id()]);
	}

	public function add(string $content, InformationSource $source) {
		if($this->isDuplicate($content))
			throw new Exception\DuplicateException('Bulletpoint již existuje');
		$this->database->query(
			'INSERT INTO bulletpoints
			(user_id, content, information_source_id, document_id)
			VALUES (?, ?, ?, ?)',
			[
				$this->myself->id(),
				$content,
				$source->id(),
				$this->document->id(),
			]
		);
	}

	private function isDuplicate(string $content): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM bulletpoints WHERE document_id = ? AND content = ?',
			[$this->document->id(), $content]
		);
	}
}