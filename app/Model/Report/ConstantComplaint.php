<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Access;

final class ConstantComplaint implements Complaint {
    private $complainer;
    private $target;
    private $reason;
    private $origin;

    public function __construct(
        Access\Identity $complainer,
        Target $target,
        string $reason,
        Complaint $origin
    ) {
        $this->complainer = $complainer;
        $this->target = $target;
        $this->reason = $reason;
        $this->origin = $origin;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function complainer(): Access\Identity {
        return $this->complainer;
    }

    public function target(): Target {
        return $this->target;
    }

    public function reason(): string {
        return $this->reason;
    }

    public function settle() {
        $this->origin->settle();
    }
}