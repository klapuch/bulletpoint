<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Model\{
    Access, Storage
};

final class OwnedMySqlComments implements Comments {
    private $owner;
    private $database;

    public function __construct(
        Access\Identity $owner,
        Storage\Database $database
    ) {
        $this->owner = $owner;
        $this->database = $database;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT posted_at, content, ID, visible
			FROM comments
			WHERE user_id = ?
			ORDER BY posted_at DESC',
            [$this->owner->id()]
        );
        foreach($rows as $row) {
            yield new ConstantComment(
                $this->owner,
                $row['content'],
                new \DateTime($row['posted_at']),
                $row['visible'],
                new MySqlComment($row['ID'], $this->owner, $this->database)
            );
        }
    }

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM comments WHERE user_id = ?',
            [$this->owner->id()]
        );
    }
}