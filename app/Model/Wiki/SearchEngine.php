<?php
namespace Bulletpoint\Model\Wiki;

interface SearchEngine {
    public function matches(string $keyword): array;
}