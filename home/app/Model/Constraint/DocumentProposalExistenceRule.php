<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;
use Bulletpoint\Core\Storage;

final class DocumentProposalExistenceRule implements Rule {
	private $database;

	public function __construct(Storage\Database $database) {
		$this->database = $database;
	}

	public function isSatisfied($input) {
		if(!$this->exists($input))
			throw new Exception\ExistenceException('Návrh neexistuje');
	}

	private function exists($input): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM document_proposals WHERE ID = ?',
			[$input]
		);
	}
}