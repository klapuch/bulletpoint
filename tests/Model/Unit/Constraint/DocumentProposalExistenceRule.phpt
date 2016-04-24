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

final class DocumentProposalExistenceRule extends TestCase\Database {
	/**
	* @throws \Bulletpoint\Exception\ExistenceException Návrh neexistuje
	*/
	public function testUnknownProposal() {
		(new Constraint\DocumentProposalExistenceRule(
			$this->preparedDatabase()
		))->isSatisfied(9);
	}

    /**
     * @throws \Bulletpoint\Exception\ExistenceException Návrh neexistuje
     */
    public function testAlreadyAddedProposal() {
        (new Constraint\DocumentProposalExistenceRule(
            $this->preparedDatabase()
        ))->isSatisfied(2);
    }

	public function testExistingProposal() {
		(new Constraint\DocumentProposalExistenceRule(
			$this->preparedDatabase()
		))->isSatisfied(1);
		Assert::true(true);
	}

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE document_proposals');
        $connection->query(
            'INSERT INTO document_proposals
            (ID, title, description, author, decision, arbiter, information_source_id)
            VALUES (1, "Valid", "ValidToo", 1, "0", NULL, 1),
            (2, "NotValid", "NotValidToo", "+1", NOW(), 1, 1)'
        );
        return $connection;
    }
}


(new DocumentProposalExistenceRule())->run();
