<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Storage;
use Bulletpoint\Model\Access;

final class OwnedMySqlBulletpoints extends Bulletpoints {
    private $identity;
    private $origin;

    public function __construct(
        Access\Identity $identity,
        Storage\Database $database,
        Bulletpoints $origin
    ) {
        parent::__construct($database);
        $this->identity = $identity;
        $this->origin = $origin;
    }

    public function iterate(): \Iterator {
        return $this->iterateBy('users.ID = ?', [$this->identity->id()]);
    }

    public function add(string $content, InformationSource $source) {
        $this->origin->add($content, $source);
    }
}