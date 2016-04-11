<?php
namespace Bulletpoint\Model\Conversation;

interface Discussion {
    public function id(): int;
    public function post(string $content);
    public function comments(): Comments;
}