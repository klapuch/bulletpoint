<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\TestCase;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class BulletpointProposalExistenceRule extends TestCase\Database {
    /**
     * @throws \Bulletpoint\Exception\ExistenceException Návrh neexistuje
     */
    public function testUnknownProposal() {
        (new Constraint\BulletpointProposalExistenceRule(
            $this->preparedDatabase()
        ))->isSatisfied(9);
    }

    /**
     * @throws \Bulletpoint\Exception\ExistenceException Návrh neexistuje
     */
    public function testAlreadyAddedProposal() {
        (new Constraint\BulletpointProposalExistenceRule(
            $this->preparedDatabase()
        ))->isSatisfied(2);
    }

    public function testExistingProposal() {
        (new Constraint\BulletpointProposalExistenceRule(
            $this->preparedDatabase()
        ))->isSatisfied(1);
        Assert::true(true);
    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE bulletpoint_proposals');
        $connection->query(
            'INSERT INTO bulletpoint_proposals
            (ID, document_id, content, author, decision, arbiter, information_source_id)
            VALUES (1, 1, "ValidToo", 1, "0", NULL, 1),
            (2, 1, "NotValidToo", "+1", NOW(), 1, 1)'
        );
        return $connection;
    }
}


(new BulletpointProposalExistenceRule())->run();
