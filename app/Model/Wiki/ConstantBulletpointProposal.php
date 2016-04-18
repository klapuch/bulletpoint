<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

final class ConstantBulletpointProposal implements BulletpointProposal {
    private $author;
    private $date;
    private $source;
    private $content;
    private $origin;
    private $document;

    public function __construct(
        Access\Identity $author,
        \DateTimeImmutable $date,
        InformationSource $source,
        string $content,
        BulletpointProposal $origin,
        Document $document
    ) {
        $this->author = $author;
        $this->date = $date;
        $this->source = $source;
        $this->content = $content;
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

    public function date(): \DateTimeImmutable {
        return $this->date;
    }

    public function document(): Document {
        return $this->document;
    }

    public function edit(string $content) {
        $this->origin->edit($content);
    }

    public function accept(): Bulletpoint {
        return $this->origin->accept();
    }

    public function reject(string $reason = null) {
        $this->origin->reject($reason);
    }
}