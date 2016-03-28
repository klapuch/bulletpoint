<?php
namespace Bulletpoint\Model\Wiki;

interface DocumentProposals {
    public function iterate(): \Iterator;
    public function propose(
        string $title,
        string $description,
        InformationSource $source
    );
}