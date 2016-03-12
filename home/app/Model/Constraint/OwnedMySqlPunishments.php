<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Core\Storage;
use Bulletpoint\Model\Access;
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
        return $this->iterateBy('sinner_id = ?', [$this->myself->id()]);
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