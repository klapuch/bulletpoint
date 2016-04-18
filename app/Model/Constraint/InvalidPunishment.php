<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\Access;

final class InvalidPunishment implements Punishment {
    private $sinner;

    public function __construct(Access\Identity $sinner) {
        $this->sinner = $sinner;
    }

    public function sinner(): Access\Identity {
        return $this->sinner;
    }

    public function id(): int {
        return 0;
    }

    public function reason(): string {
        return '';
    }

    public function expired(): bool {
        return true;
    }

    public function expiration(): \DateTimeImmutable {
        return new \DateTimeImmutable('yesterday');
    }

    public function forgive() {
        // Do nothing
    }
}