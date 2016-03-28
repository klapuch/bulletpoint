<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\{Storage, Access};
use Bulletpoint\Exception;

final class ActualMySqlPunishments extends Punishments {
    public function iterate(): \Iterator {
        return $this->iterateBy('forgiven = 0 AND NOW() < expiration');
    }

    public function punish(
        Access\Identity $sinner,
        \DateTime $expiration,
        string $reason
    ) {
        if($this->past($expiration)) {
            throw new \LogicException(
                'Trest smí být udělen pouze na budoucí období'
            );
        }
        parent::punish($sinner, $expiration, $reason);
    }

    private function past(\DateTime $expiration): bool {
        return $expiration < new \DateTime;
    }
}