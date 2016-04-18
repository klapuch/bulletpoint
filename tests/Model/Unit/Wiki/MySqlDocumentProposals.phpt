<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Wiki;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlDocumentProposals extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO users (ID, role, username) VALUES (2, "member", "cucak")'
		);
		$connection->query(
			'INSERT INTO document_proposals
			(title, description, author, decision, information_source_id, proposed_at)
			VALUES ("titulek", "fooContent", 2, "0", 1, "2000-01-01 01:01:01"),
			("titulek2", "fooContent2", 2, "+1", 1, "2000-01-01 01:01:01")'
		);
		$connection->query(
			'INSERT INTO information_sources
			(place, `year`, author)
			VALUES ("wikipedie", 2000, "facedown")'
		);
		$admin = new Fake\Identity(1);
		$rows = (new Wiki\MySqlDocumentProposals(
			$admin,
			$connection
		))->iterate();
		Assert::equal(
			new Wiki\ConstantDocumentProposal(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'member',
						new Access\MySqlRole(2, $connection)
					),
					'cucak'
				),
				new \DateTimeImmutable('2000-01-01 01:01:01'),
				new Wiki\ConstantInformationSource(
					'wikipedie',
					2000,
					'facedown',
					new Wiki\MySqlInformationSource(1, $connection)
				),
				'titulek',
				'fooContent',
				new Wiki\MySqlDocumentProposal(1, $admin, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	public function testProposing() {
		$connection = $this->preparedDatabase();
		(new Wiki\MySqlDocumentProposals(new Fake\Identity(1), $connection))
		->propose(
			'barTitle',
			'barContent',
			new Fake\InformationSource(10, 'wikipedie', 1999, 'facedown')
		);
		Assert::same(
			[
				'title' => 'barTitle',
				'description' => 'barContent',
				'information_source_id' => 10
			],
			$connection->fetch(
				'SELECT title, description, information_source_id
				FROM document_proposals'
			)
		);
	}

	/**
	* @throws \Bulletpoint\Exception\DuplicateException Dokument již existuje
	*/
	public function testProposingExistingDocument() {
		(new Wiki\MySqlDocumentProposals(
			new Fake\Identity(1),
			new Fake\Database($fetch = 'alreadyExists-ThisToBoolean')
		))->propose(
			'barTitle',
			'barContent',
			new Fake\InformationSource(10)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_proposals');
		return $connection;
	}
}


(new MySqlDocumentProposals())->run();
