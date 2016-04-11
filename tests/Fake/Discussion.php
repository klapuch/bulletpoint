<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Conversation;

final class Discussion implements Conversation\Discussion {
    private $id;

    public function __construct(int $id = null) {
        $this->id = $id;
    }

    public function id(): int {
        return $this->id;
    }

    public function post(string $content) {
        // TODO: Implement post() method.
    }

    public function comments(): Conversation\Comments {
        // TODO: Implement comments() method.
    }
}