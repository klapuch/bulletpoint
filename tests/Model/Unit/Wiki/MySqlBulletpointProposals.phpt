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

final class MySqlBulletpointProposals extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$connection->query('TRUNCATE users');
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO users (ID, role, username) VALUES (2, "user", "facedown")'
		);
		$connection->query(
			'INSERT INTO bulletpoint_proposals
			(document_id, content, author, decision, information_source_id, proposed_at)
			VALUES (100, "fooContent", 2, "0", 1, "2000-01-01 01:01:01"),
			(9, "fooContent2", 2, "+1", 1, "2000-01-01 01:01:01")'
		);
		$connection->query(
			'INSERT INTO information_sources
			(place, `year`, author)
			VALUES ("wikipedie", 2000, "facedown")'
		);
		$admin = new Fake\Identity(1);
		$rows = (new Wiki\MySqlBulletpointProposals($admin, $connection))
		->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpointProposal(
				new Access\ConstantIdentity(
					2,
					new Access\ConstantRole(
						'user',
						new Access\MySqlRole(2, $connection)
					),
					'facedown'
				),
				new \Datetime('2000-01-01 01:01:01'),
				new Wiki\ConstantInformationSource(
					'wikipedie',
					2000,
					'facedown',
					new Wiki\MySqlInformationSource(1, $connection)
				),
				'fooContent',
				new Wiki\MySqlDocument(100, $connection),
				new Wiki\MySqlBulletpointProposal(1, $admin, $connection)
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

	public function testProposing() {
		$connection = $this->preparedDatabase();
		(new Wiki\MySqlBulletpointProposals(
			new Fake\Identity(1),
			$connection
		))->propose(
			new Fake\Document(100),
			'barContent',
			new Fake\InformationSource(10, 'wikipedie', 1999, 'facedown')
		);
		Assert::same(
			[
				'content' => 'barContent',
				'information_source_id' => 10,
				'document_id' => 100
			],
			$connection->fetch(
				'SELECT content, information_source_id, document_id
				FROM bulletpoint_proposals'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
	*/
	public function testProposingExistingBulletpoint() {
		(new Wiki\MySqlBulletpointProposals(
			new Fake\Identity(1),
			new Fake\Database($fetch = 'alreadyExists-ThisToBoolean')
		))->propose(
			new Fake\Document(100),
			'barContent',
			new Fake\InformationSource(10)
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_proposals');
		return $connection;
	}
}


(new MySqlBulletpointProposals())->run();
