<?php
namespace Bulletpoint\Model\Conversation;

interface Comments {
    public function iterate(): \Iterator;
    public function count(): int;
}