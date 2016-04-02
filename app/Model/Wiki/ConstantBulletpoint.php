<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

final class ConstantBulletpoint implements Bulletpoint {
    private $author;
    private $content;
    private $date;
    private $source;
    private $origin;
    private $document;

    public function __construct(
        Access\Identity $author,
        string $content,
        \DateTime $date,
        InformationSource $source,
        Bulletpoint $origin,
        Document $document = null
    ) {
        $this->author = $author;
        $this->content = $content;
        $this->date = $date;
        $this->source = $source;
        $this->origin = $origin;
        $this->document = $document;
    }

    public function author(): Access\Identity {
        return $this->author;
    }

    public function content(): string {
        return $this->content;
    }

    public function source(): InformationSource {
        return $this->source;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function date(): \DateTime {
        return $this->date;
    }

    public function document(): Document {
        if($this->document === null)
            return $this->origin->document();
        return $this->document;
    }

    public function edit(string $content) {
        $this->origin->edit($content);
    }
}