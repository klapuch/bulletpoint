<?php
namespace Bulletpoint\Model\Wiki;

interface Bulletpoints {
    public function iterate(): array;
    public function add(
        string $content,
        Document $document,
        InformationSource $source
    );
    public function count(): int;
}