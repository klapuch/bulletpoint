<?php
namespace Bulletpoint\Model\Wiki;

final class ReversedBulletpoints implements Bulletpoints {
    private $origin;

    public function __construct(Bulletpoints $origin) {
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        return new \ArrayIterator(array_reverse(iterator_to_array($this->origin->iterate())));
    }

    public function add(
        string $content,
        Document $document,
        InformationSource $source
    ) {
        return $this->origin->add($content, $document, $source);
    }

    public function count(): int {
        return $this->origin->count();
    }
}