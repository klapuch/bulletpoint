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

final class MySqlBulletpointProposal extends TestCase\Database {
	public function testContent() {
		Assert::same(
			'first',
			(new Wiki\MySqlBulletpointProposal(
				1,
				new Fake\Identity(1),
				$this->preparedBulletpoints()
			))->content()
		);
	}

	public function testDocument() {
		$connection = $this->preparedBulletpoints();
		Assert::equal(
			new Wiki\MySqlDocument(1, $connection),
			(new Wiki\MySqlBulletpointProposal(
				1,
				new Fake\Identity(1),
				$this->preparedBulletpoints()
			))->document()
		);
	}	

	public function testDate() {
		Assert::equal(
			new \Datetime('2000-01-01 01:01:01'),
			(new Wiki\MySqlBulletpointProposal(
				1,
				new Fake\Identity(1),
				$this->preparedBulletpoints()
			))->date()
		);
	}

	public function testId() {
		Assert::same(
			2,
			(new Wiki\MySqlBulletpointProposal(
				2,
				new Fake\Identity(1),
				new Fake\Database
			))->id()
		);
	}

	public function testAuthor() {
		$this->preparedBulletpoints();
		$this->preparedUsers();
		Assert::equal(
			new Access\MySqlIdentity(1, $this->connection()),
			(new Wiki\MySqlBulletpointProposal(
				1,
				new Fake\Identity(1),
				$this->connection()
			))->author()
		);
	}

	public function testSource() {
		$connection = $this->preparedBulletpoints();
		Assert::equal(
			new Wiki\MySqlInformationSource(1, $connection),
			(new Wiki\MySqlBulletpointProposal(
				1,
				new Fake\Identity(1),
				$connection
			))->source()
		);
	}

	public function testEditing() {
		$connection = $this->preparedBulletpoints();
		$connection->query('TRUNCATE bulletpoints');
		(new Wiki\MySqlBulletpointProposal(
			1,
			new Fake\Identity(1),
			$connection
		))->edit('newContent');
		Assert::same(
			[
				'content' => 'newContent',
				'proposed_at' => '2000-01-01 01:01:01'
			],
			$connection->fetch(
				'SELECT content, proposed_at
				FROM bulletpoint_proposals
				WHERE ID = 1'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
	*/
	public function testEditingToExistingOne() {
		$connection = $this->preparedBulletpoints();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query(
			'INSERT INTO bulletpoints (document_id, content) VALUES (1, "foo")'
		);
		$connection->query('TRUNCATE bulletpoint_proposals');
		$connection->query(
			'INSERT INTO bulletpoint_proposals (document_id, content) VALUES (1, "bar")'
		);
		$proposal = new Wiki\MySqlBulletpointProposal(1, new Fake\Identity(1), $connection);
		$proposal->edit('foo');
	}

	public function testAccepting() {
		$connection = $this->preparedProposals();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, `year`, author)
			VALUES (1, "wikipedie", 2000, "facedown")'
		);
		Assert::same(
			[
				'decision' => '0',
				'arbiter' => null,
				'document_id' => 1,
				'decided_at' => null,
				'arbiter_comment' => null,
			],
			$connection->fetch(
				'SELECT decision,
				arbiter,
				document_id,
				LENGTH(decided_at) AS decided_at,
				arbiter_comment
				FROM bulletpoint_proposals'
			)
		);
		$acceptedBulletpoint = (new Wiki\MySqlBulletpointProposal(
			1,
			new Fake\Identity(1),
			$connection
		))->accept();
		Assert::equal(
			$acceptedBulletpoint,
			new Wiki\MySqlBulletpoint(1, $connection)
		);
		Assert::same(
			[
				'user_id' => 2,
				'information_source_id' => 1,
				'content' => 'fooText',
				'document_id' => 1
			],
			$connection->fetch(
				'SELECT user_id, information_source_id, content, document_id
				FROM bulletpoints'
			)
		);
		Assert::same(
			[
				'decision' => '+1',
				'document_id' => 1,
				'arbiter' => 1,
				'decided_at' => 19, // length, check emptiness
				'arbiter_comment' => null,
			],
			$connection->fetch('SELECT decision, document_id,
			arbiter, LENGTH(decided_at) AS decided_at, arbiter_comment
			FROM bulletpoint_proposals')
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
	*/
	public function testAcceptingExistingOne() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query('TRUNCATE bulletpoint_proposals');
		$connection->query(
			'INSERT INTO bulletpoint_proposals (document_id, content)
			VALUES (1, "foo")'
		);		
		$connection->query(
			'INSERT INTO bulletpoints (document_id, content)
			VALUES (1, "foo")'
		);
		(new Wiki\MySqlBulletpointProposal(
			1,
			new Fake\Identity(1),
			$connection
		))->accept();
	}

	public function testRejecting() {
		$connection = $this->preparedProposals();
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, `year`, author)
			VALUES (1, "wikipedie", 2000, "facedown")'
		);
		Assert::same(
			[
				'decision' => '0',
				'arbiter' => null,
				'decided_at' => null,
				'arbiter_comment' => null,
			],
			$connection->fetch(
				'SELECT decision,
				arbiter,
				LENGTH(decided_at) AS decided_at,
				arbiter_comment
				FROM bulletpoint_proposals'
			)
		);
		(new Wiki\MySqlBulletpointProposal(
			1,
			new Fake\Identity(1),
			$connection,
			new Wiki\MySqlInformationSources($connection)
		))->reject('nonsense');
		Assert::same(
			[
				'decision' => '-1',
				'arbiter' => 1,
				'decided_at' => 19, // length, check emptiness
				'arbiter_comment' => 'nonsense',
			],
			$connection->fetch('SELECT decision,
			arbiter, LENGTH(decided_at) AS decided_at,
			arbiter_comment FROM bulletpoint_proposals')
		);
	}

	private function preparedProposals() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_proposals');
		$connection->query(
			'INSERT INTO bulletpoint_proposals
			(document_id, content, author, information_source_id)
			VALUES (1, "fooText", 2, 1)'
		);
		return $connection;
	}

	private function preparedInformationSources() {
		$connection = $this->connection();
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, author, `year`)
			VALUES (1, "wikipedie", "facedown", 2005),
			(2, "some book", "čapek", 1998)'
		);
		return $connection;
	}

	private function preparedBulletpoints() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoint_proposals');
		$connection->query(
			'INSERT INTO bulletpoint_proposals
			(ID, content, author, information_source_id, proposed_at, document_id)
			VALUES (1, "first", 1, 1, "2000-01-01 01:01:01", 1),
			(2, "second", 2, 2, "2000-01-01 01:01:01", 2)'
		);
		return $connection;
	}

	private function preparedUsers() {
		$connection = $this->connection();
		$connection->query('TRUNCATE users');
		$connection->query(
			'INSERT INTO users (ID, role) VALUES (1, "user")'
		);
		return $connection;
	}
}


(new MySqlBulletpointProposal())->run();
