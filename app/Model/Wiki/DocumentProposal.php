<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface DocumentProposal {
    public function id(): int;
    public function author(): Access\Identity;
    public function date(): \DateTimeImmutable;
    public function source(): InformationSource;
    public function title(): string;
    public function description(): string;
    public function edit(string $title, string $description);
    public function accept(): Document;
    public function reject(string $reason = null);
}