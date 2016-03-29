<?php
namespace Bulletpoint\Model\Wiki;

interface Bulletpoints {
    public function iterate(): \Iterator;
    public function add(string $content, InformationSource $source);
}