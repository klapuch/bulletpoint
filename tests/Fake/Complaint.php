<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{Access, Report};

final class Complaint implements Report\Complaint {
    private $reason;
    private $id;

    public function __construct(string $reason = '', int $id = 0) {
        $this->reason = $reason;
        $this->id = $id;
    }

    public function id(): int {
        $this->id;
    }

    public function complainer(): Access\Identity {

    }

    public function target(): Report\Target {

    }

    public function reason(): string {
        return $this->reason;
    }

    public function settle() {

    }
}