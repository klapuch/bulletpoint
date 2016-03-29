<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\{
    Storage, Access
};

interface Punishments {
    public function iterate(): \Iterator;
    public function punish(
        Access\Identity $sinner,
        \DateTime $expiration,
        string $reason
    );
}