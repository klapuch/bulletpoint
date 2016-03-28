<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;
use Bulletpoint\Model\Storage;

final class BulletpointProposalExistenceRule implements Rule {
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function isSatisfied($input) {
        if(!$this->exists($input))
            throw new Exception\ExistenceException('Návrh neexistuje');
    }

    private function exists($input): bool {
        return (bool)$this->database->fetch(
            'SELECT 1 FROM bulletpoint_proposals WHERE ID = ?',
            [$input]
        );
    }
}