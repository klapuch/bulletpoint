<?php
namespace Bulletpoint\Model\Wiki;

interface InformationSources {
    public function add(
        string $place,
        $year,
        string $author
    ): InformationSource;
}