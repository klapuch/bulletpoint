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

final class MySqlDocumentProposal extends TestCase\Database {
public function testDescription() {
		Assert::same(
			'foo description',
			(new Wiki\MySqlDocumentProposal(
				1,
				new Fake\Identity(1),
				$this->preparedDocuments()
			))->description()
		);
	}

	public function testTitle() {
		Assert::same(
			'foo title',
			(new Wiki\MySqlDocumentProposal(
				1,
				new Fake\Identity(1),
				$this->preparedDocuments()
			))->title()
		);
	}

	public function testDate() {
		Assert::equal(
			new \DateTimeImmutable('2000-01-01 01:01:01'),
			(new Wiki\MySqlDocumentProposal(
				1,
				new Fake\Identity(1),
				$this->preparedDocuments()
			))->date()
		);
	}

	public function testAuthor() {
		$this->preparedUsers();
		$this->preparedDocuments();
		Assert::equal(
			new Access\MySqlIdentity(1, $this->connection()),
			(new Wiki\MySqlDocumentProposal(
				1,
				new Fake\Identity(1),
				$this->connection()
			))->author()
		);
	}

	public function testSource() {
		$connection = $this->preparedInformationSources();
		Assert::equal(
			new Wiki\MySqlInformationSource(1, $connection),
			(new Wiki\MySqlDocumentProposal(
				1,
				new Fake\Identity(1),
				$connection
			))->source()
		);
	}


	public function testId() {
		Assert::same(
			2,
			(new Wiki\MySqlDocumentProposal(
				2,
				new Fake\Identity(1),
				new Fake\Database
			))->id()
		);
	}

	public function testEditing() {
		$connection = $this->preparedDocuments();
		$connection->query('TRUNCATE documents');
		$proposal = new Wiki\MySqlDocumentProposal(1, new Fake\Identity(1), $connection);
		$proposal->edit('newTitle', 'newDescription');
		Assert::same(
			[
				'title' => 'newTitle',
				'description' => 'newDescription',
				'proposed_at' => '2000-01-01 01:01:01'
			],
			$connection->fetch(
				'SELECT title, description, proposed_at FROM document_proposals'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Titulek již existuje
	*/
	public function testEditingToExistingOne() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		$connection->query('INSERT INTO documents (ID, title) VALUES (1, "fooo")');
		$proposal = new Wiki\MySqlDocumentProposal(2, new Fake\Identity(1), $connection);
		$proposal->edit('fooo', 'whatever');
	}

	public function testAccepting() {
		$connection = $this->preparedProposals();
		$connection->query('TRUNCATE documents');
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
				FROM document_proposals'
			)
		);
		$acceptedDocument = (new Wiki\MySqlDocumentProposal(
			1,
			new Fake\Identity(1),
			$connection
		))->accept();
		Assert::equal(
			$acceptedDocument,
			new Wiki\MySqlDocument(1, $connection)
		);
		Assert::same(
			[
				'user_id' => 2,
				'information_source_id' => 1,
				'description' => 'fooText',
				'title' => 'fooTitle',
			],
			$connection->fetch(
				'SELECT user_id, information_source_id, description, title
				FROM documents'
			)
		);
		Assert::same(
			[
				'decision' => '+1',
				'arbiter' => 1,
				'decided_at' => 19, // length, check emptiness
				'arbiter_comment' => null,
			],
			$connection->fetch('SELECT decision,
			arbiter, LENGTH(decided_at) AS decided_at,
			arbiter_comment FROM document_proposals')
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Titulek již existuje
	*/
	public function testAcceptingExistingOne() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		$connection->query('TRUNCATE document_proposals');
		$connection->query(
			'INSERT INTO document_proposals (title) VALUES ("foo")'
		);		
		$connection->query(
			'INSERT INTO documents (title) VALUES ("foo")'
		);
		(new Wiki\MySqlDocumentProposal(
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
				FROM document_proposals'
			)
		);
		(new Wiki\MySqlDocumentProposal(
			1,
			new Fake\Identity(1),
			$connection
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
			arbiter_comment FROM document_proposals')
		);
	}

	private function preparedProposals() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_proposals');
		$connection->query(
			'INSERT INTO document_proposals
			(title, description, author, information_source_id)
			VALUES ("fooTitle", "fooText", 2, 1)'
		);
		return $connection;
	}

	private function preparedDocuments() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_proposals');
		$connection->query(
			'INSERT INTO document_proposals
			(ID, author, proposed_at, description, information_source_id, title)
			VALUES
			(1, 1, "2000-01-01 01:01:01", "foo description", 1, "foo title"),
			(2, 1, "2000-01-01 01:01:01", "blehDescription", 1, "blahTitle")'
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

	private function preparedInformationSources() {
		$connection = $this->connection();
		$connection->query('TRUNCATE information_sources');
		$this->preparedDocuments();
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, author, `year`)
			VALUES (1, "wikipedie", "facedown", 2005)'
		);
		return $connection;
	}
}


(new MySqlDocumentProposal())->run();
