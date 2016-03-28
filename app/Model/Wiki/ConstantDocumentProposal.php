<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

final class ConstantDocumentProposal implements DocumentProposal {
    private $author;
    private $date;
    private $source;
    private $title;
    private $description;
    private $origin;

    public function __construct(
        Access\Identity $author,
        \DateTime $date,
        InformationSource $source,
        string $title,
        string $description,
        DocumentProposal $origin
    ) {
        $this->author = $author;
        $this->date = $date;
        $this->source = $source;
        $this->title = $title;
        $this->description = $description;
        $this->origin = $origin;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function author(): Access\Identity {
        return $this->author;
    }

    public function date(): \DateTime {
        return $this->date;
    }

    public function source(): InformationSource {
        return $this->source;
    }

    public function title(): string {
        return $this->title;
    }

    public function description(): string {
        return $this->description;
    }

    public function edit(string $title, string $description) {
        $this->origin->edit($title, $description);
    }

    public function accept(): Document {
        return $this->origin->accept();
    }

    public function reject(string $reason = null) {
        $this->origin->reject($reason);
    }
}