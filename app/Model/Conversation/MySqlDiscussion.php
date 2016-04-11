<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Model\{
    Storage, Access
};

final class MySqlDiscussion implements Discussion {
    private $id;
    private $myself;
    private $database;

    public function __construct(
        int $id,
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->id = $id;
        $this->myself = $myself;
        $this->database = $database;
    }

    public function id(): int {
        return $this->id;
    }

    public function comments(): Comments {
        return new InDiscussionMySqlComments(
            $this,
            $this->myself,
            $this->database
        );
    }

    public function post(string $content) {
        $this->database->query(
            'INSERT INTO comments (user_id, content, document_id)
			VALUES (?, ?, ?)',
            [$this->myself->id(), $content, $this->id]
        );
    }
}