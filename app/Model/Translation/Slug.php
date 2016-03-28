<?php
namespace Bulletpoint\Model\Translation;

interface Slug {
    public function origin(): int;
    public function rename(string $newSlug): self;
    public function __toString();
}