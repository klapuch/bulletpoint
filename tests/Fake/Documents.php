<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Wiki;

final class Documents implements Wiki\Documents{
    public function iterate(): \Iterator {
    }

    public function add(
        string $title,
        string $description,
        Wiki\InformationSource $source
    ): Wiki\Document {
    }
}