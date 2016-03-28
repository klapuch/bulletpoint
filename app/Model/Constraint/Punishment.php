<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\Access;

interface Punishment {
    public function sinner(): Access\Identity;
    public function id(): int;
    public function reason(): string;
    public function expired(): bool;
    public function expiration(): \DateTime;
    public function forgive();
}