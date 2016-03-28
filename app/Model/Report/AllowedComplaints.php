<?php
namespace Bulletpoint\Model\Report;

final class AllowedComplaints implements Complaints {
    private $origin;
    const DEFAULT_REASON = 'Jiné';
    const ALLOWED_REASONS = [
        'vulgarita',
        'spam',
        self::DEFAULT_REASON,
    ];

    public function __construct(Complaints $origin) {
        $this->origin = $origin;
    }

    public function iterate(Target $target): \Iterator {
        return $this->origin->iterate($target);
    }

    public function settle(Target $target) {
        $this->origin->settle($target);
    }

    public function complain(Target $target, string $reason): Complaint {
        if(!$this->isReasonAllowed($reason))
            return $this->complain($target, self::DEFAULT_REASON);
        return $this->origin->complain($target, $reason);
    }

    private function isReasonAllowed(string $reason): bool {
        return in_array(
            mb_strtolower($reason),
            array_map('mb_strtolower', self::ALLOWED_REASONS)
        );
    }
}