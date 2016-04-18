<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface Document {
    public function author(): Access\Identity;
    public function title(): string;
    public function description(): string;
    public function source(): InformationSource;
    public function id(): int;
    public function date(): \DateTimeImmutable;
    public function edit(string $title, string $description);
}