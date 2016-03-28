<?php
namespace Bulletpoint\Model\Translation;

interface Slugs {
    public function add(int $origin, string $slug): Slug;
}