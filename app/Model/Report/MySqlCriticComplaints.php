<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\{
    Access, Storage
};

final class MySqlCriticComplaints implements Complaints {
    private $critic;
    private $database;
    private $origin;

    public function __construct(
        Access\Identity $critic,
        Storage\Database $database,
        Complaints $origin
    ) {
        $this->critic = $critic;
        $this->database = $database;
        $this->origin = $origin;
    }

    public function iterate(Target $target): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT comment_complaints.ID,
            comment_id AS target,
			reason,
			user_id,
			users.role,
			users.username
			FROM comment_complaints
			INNER JOIN users
			ON users.ID = user_id
			WHERE comment_id = ? AND user_id = ? AND settled = 0',
            [$target->id(), $this->critic->id()]
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
                new MySqlComplaint($row['ID'], $this->critic, $this->database)
            );
        }
    }

    public function complain(Target $target, string $reason): Complaint {
        if($this->alreadyComplained($target))
            throw new \OverflowException('Tento komentář jsi již nahlásil');
        return $this->origin->complain($target, $reason);
    }

    public function settle(Target $target) {
        $this->origin->settle($target);
    }


    private function alreadyComplained(Target $target): bool {
        return (bool)$this->database->fetch(
            'SELECT 1
			FROM comment_complaints
			WHERE comment_id = ? AND user_id = ? AND settled = 0',
            [$target->id(), $this->critic->id()]
        );
    }
}