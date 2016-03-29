<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Model\{
    Storage, Access
};
use Bulletpoint\Exception;

final class OwnedMySqlPunishments implements Punishments {
    private $sinner;
    private $database;
    private $origin;

    public function __construct(
        Access\Identity $sinner,
        Storage\Database $database,
        Punishments $origin
    ) {
        $this->sinner = $sinner;
        $this->database = $database;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        $rows = $this->database->fetchAll(
            'SELECT ID, reason, expiration
			FROM punishments
			WHERE sinner_id = ?
			ORDER BY forgiven ASC, expiration DESC',
            [$this->sinner->id()]
        );
        if(!$rows)
            yield from [new InvalidPunishment($this->sinner)];
        foreach($rows as $row) {
            yield new ConstantPunishment(
                $this->sinner,
                $row['reason'],
                new \DateTime($row['expiration']),
                new MySqlPunishment($row['ID'], $this->database)
            );
        }
    }

    public function punish(
        Access\Identity $sinner,
        \DateTime $expiration,
        string $reason
    ) {
        if($sinner->id() === $this->sinner->id())
            throw new \LogicException('Nemůžeš potrestat sám sebe');
        $this->origin->punish($sinner, $expiration, $reason);
    }
}