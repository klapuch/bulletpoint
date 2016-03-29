<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\{
    Access, Constraint
};

final class Punishments implements Constraint\Punishments {
    public function iterate(): \Iterator {

    }

    public function punish(
        Access\Identity $sinner,
        \DateTime $expiration,
        string $reason
    ) {
        // Do nothing
    }
}