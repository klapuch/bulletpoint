<?php
namespace Bulletpoint\Model\Wiki;

interface InformationSources {
    public function create(
        string $place,
        $year,
        string $author
    ): InformationSource;
}