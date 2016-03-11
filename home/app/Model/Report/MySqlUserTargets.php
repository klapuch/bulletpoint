<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;

final class MySqlUserTargets implements Targets {
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
			'SELECT comment_id, ID
			FROM comment_complaints
			WHERE user_id = ? AND settled = 0',
			[$this->myself->id()]
		);
		foreach($rows as $row) {
			yield new ConstantTarget(
				$row['comment_id'],
				new \ArrayIterator(
					[
						new MySqlComplaint(
							$row['ID'],
							$this->myself,
							$this->database
						)
					]
				)
			);
		}
	}
}