<?php
namespace Bulletpoint\Model\Conversation;

use Bulletpoint\Model\Access;

final class ConstantComment implements Comment {
    private $author;
    private $content;
    private $date;
    private $visible;
    private $origin;

    public function __construct(
        Access\Identity $author,
        string $content,
        \DateTime $date,
        bool $visible,
        Comment $origin
    ) {
        $this->author = $author;
        $this->content = $content;
        $this->date = $date;
        $this->visible = $visible;
        $this->origin = $origin;
    }

    public function author(): Access\Identity {
        return $this->author;
    }

    public function content(): string {
        return $this->content;
    }

    public function date(): \DateTime {
        return $this->date;
    }

    public function edit(string $content) {
        $this->origin->edit($content);
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function visible(): bool {
        return $this->visible;
    }

    public function erase() {
        $this->origin->erase();
    }
}