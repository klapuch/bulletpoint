<?php
namespace Bulletpoint\Model\Access;

interface Role {
    const DEFAULT_ROLE = 'guest';
    const DEFAULT_RANK = -1;
    public function promote(): self;
    public function degrade(): self;
    public function rank(): int;
    public function __toString();
}