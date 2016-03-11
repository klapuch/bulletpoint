<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Access;
use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class MySqlComplaints implements Complaints {
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
			'SELECT COUNT(ID) AS count,
			reason,
			comment_id AS target
			FROM comment_complaints
			WHERE settled = 0
			GROUP BY target, reason
			ORDER BY count DESC, complained_at DESC'
		);
		foreach($rows as $row) {
			yield $row['count'] => [
				'reason' => $row['reason'],
				'target' => $row['target']
			];
		}
	}

	public function settle(Target $target) {
		$this->database->query(
			'UPDATE comment_complaints SET settled = 1 WHERE comment_id = ?',
			[$target->id()]
		);
	}

	public function complain(Target $target, string $reason) {
		if($this->exists($target))
			throw new \OverflowException('Tento komentář jsi již nahlásil');
		$this->database->query(
			'INSERT INTO comment_complaints (user_id, reason, comment_id)
			VALUES (?, ?, ?)',
			[$this->myself->id(), $reason, $target->id()]
		);
	}

	private function exists(Target $target): bool {
		return (bool)$this->database->fetch(
			'SELECT 1
			FROM comment_complaints
			WHERE comment_id = ? AND user_id = ? AND settled = 0',
			[$target->id(), $this->myself->id()]
		);
	}
}