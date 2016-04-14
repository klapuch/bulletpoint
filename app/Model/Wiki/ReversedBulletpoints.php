<?php
namespace Bulletpoint\Model\Wiki;

final class ReversedBulletpoints implements Bulletpoints {
    private $origin;

    public function __construct(Bulletpoints $origin) {
        $this->origin = $origin;
    }

    public function iterate(): array {
        return array_reverse($this->origin->iterate());
    }

    public function add(
        string $content,
        Document $document,
        InformationSource $source
    ) {
        $this->origin->add($content, $document, $source);
    }

    public function count(): int {
        return $this->origin->count();
    }
}