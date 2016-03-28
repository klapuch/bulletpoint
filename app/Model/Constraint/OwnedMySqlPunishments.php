<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class OwnedMySqlPunishments extends Punishments {
    private $origin;

    public function __construct(
        Access\Identity $myself,
        Storage\Database $database,
        Punishments $origin
    ) {
        parent::__construct($myself, $database);
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $punishments = $this->iterateBy('sinner_id = ?', [$this->myself->id()]);
        if($punishments->valid())
            return $punishments;
        return new \ArrayIterator([new InvalidPunishment($this->myself)]);
    }

    public function punish(
        Access\Identity $sinner,
        \DateTime $expiration,
        string $reason
    ) {
        if($sinner->id() === $this->myself->id())
            throw new \LogicException('Nemůžeš potrestat sám sebe');
        $this->origin->punish($sinner, $expiration, $reason);
    }
}