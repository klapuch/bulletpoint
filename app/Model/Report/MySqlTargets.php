<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Storage;

final class MySqlTargets implements Targets {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function iterate(): \Iterator {
        $query = 'SELECT COUNT(comment_complaints.ID) AS total,
			reason,
			comment_id AS target
			FROM comment_complaints
			LEFT JOIN comments
			ON comments.ID = comment_complaints.comment_id
			WHERE visible = 1 AND settled = 0 
			GROUP BY target, reason
			ORDER BY total DESC
			LIMIT 20';
        foreach($this->database->query($query) as $row) {
            yield [
                'total' => $row['total'],
                'reason' => $row['reason'],
                'target' => new Target($row['target']),
            ];
        }
    }
}