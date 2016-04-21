<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\Access;

interface Point {
    public function id(): int;
    public function value(): int;
    public function voter(): Access\Identity;
}