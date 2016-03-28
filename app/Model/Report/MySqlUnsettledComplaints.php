<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\{
    Access, Storage
};
use Bulletpoint\Exception;

final class MySqlUnsettledComplaints implements Complaints {
    private $myself;
    private $database;

    public function __construct(
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->myself = $myself;
        $this->database = $database;
    }

    public function iterate(Target $target): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT comment_complaints.ID,
            comment_id AS target,
			reason,
			comment_complaints.user_id,
			users.role,
			users.username
			FROM comment_complaints
			LEFT JOIN comments
			ON comments.ID = comment_id
			INNER JOIN users
			ON users.ID = comment_complaints.user_id
			WHERE comment_id = ? AND visible = 1 AND settled = 0',
            [$target->id()]
        );
        foreach($rows as $row) {
            yield new ConstantComplaint(
                new Access\ConstantIdentity(
                    $row['user_id'],
                    new Access\ConstantRole(
                        $row['role'],
                        new Access\MySqlRole($row['user_id'], $this->database)
                    ),
                    $row['username']
                ),
                new Target($row['target']),
                $row['reason'],
                new MySqlComplaint($row['ID'], $this->myself, $this->database)
            );
        }
    }

    public function settle(Target $target) {
        $this->database->query(
            'UPDATE comment_complaints
             SET settled = 1
             WHERE comment_id = ?',
            [$target->id()]
        );
    }

    public function complain(Target $target, string $reason): Complaint {
        $this->database->query(
            'INSERT INTO comment_complaints (user_id, reason, comment_id)
			VALUES (?, ?, ?)',
            [$this->myself->id(), $reason, $target->id()]
        );
        return new MySqlComplaint(
            $this->database->fetchColumn('SELECT LAST_INSERT_ID()'),
            $this->myself,
            $this->database
        );
    }
}