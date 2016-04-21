<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\Access;

final class ConstantPoint implements Point {
    private $value;
    private $voter;
    private $origin;

    public function __construct(
        int $value,
        Access\Identity $voter,
        Point $origin
    ) {
        $this->value = $value;
        $this->voter = $voter;
        $this->origin = $origin;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function value(): int {
        return $this->value;
    }

    public function voter(): Access\Identity {
        return $this->voter;
    }
}