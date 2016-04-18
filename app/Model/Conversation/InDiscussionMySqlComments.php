<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Model\{
    Storage, Access
};

final class InDiscussionMySqlComments implements Comments {
    private $discussion;
    private $database;
    private $myself;

    public function __construct(
        Discussion $discussion,
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->discussion = $discussion;
        $this->database = $database;
        $this->myself = $myself;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT users.ID AS user_id,
			users.role,
			users.username,
			comments.posted_at,
			comments.content,
			comments.ID, 
			comments.visible
			FROM comments
			INNER JOIN users
			ON users.ID = comments.user_id
			WHERE comments.document_id = ?
			ORDER BY comments.posted_at DESC',
            [$this->discussion->id()]
        );
        foreach($rows as $row) {
            yield new ConstantComment(
                new Access\ConstantIdentity(
                    $row['user_id'],
                    new Access\ConstantRole(
                        $row['role'],
                        new Access\MySqlRole($row['user_id'], $this->database)
                    ),
                    $row['username']
                ),
                $row['content'],
                new \DateTimeImmutable($row['posted_at']),
                $row['visible'],
                new MySqlComment($row['ID'], $this->myself, $this->database)
            );
        }
    }

    public function count(): int {
        return $this->database->fetchColumn(
            'SELECT COUNT(*) FROM comments WHERE document_id = ?',
            [$this->discussion->id()]
        );
    }
}