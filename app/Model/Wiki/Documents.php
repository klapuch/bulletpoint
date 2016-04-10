<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface Documents {
    public function iterate(): \Iterator;
    public function add(
        string $title,
        string $description,
        InformationSource $source
    ): Document;
    public function count(): int;
}