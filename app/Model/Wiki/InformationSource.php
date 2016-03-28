<?php
namespace Bulletpoint\Model\Wiki;

interface InformationSource {
    public function id(): int;
    public function place(): string;
    /**
     * @return int|null
     */
    public function year();
    public function author(): string;
    public function edit(string $place, $year, string $author);
}