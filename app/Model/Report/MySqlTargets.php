<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Storage;

final class MySqlTargets implements Targets {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT COUNT(ID) AS total,
			reason,
			comment_id AS target
			FROM comment_complaints
			WHERE settled = 0
			GROUP BY target, reason
			ORDER BY total DESC
			LIMIT 20'
        );
        foreach($rows as $row) {
            yield [
                'total' => $row['total'],
                'reason' => $row['reason'],
                'target' => new Target($row['target']),
            ];
        }
    }
}