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

final class MySqlDocument extends TestCase\Database {
	public function testDescription() {
		Assert::same(
			'foo description',
			(new Wiki\MySqlDocument(
				1,
				$this->preparedDocuments()
			))->description()
		);
	}

	public function testTitle() {
		Assert::same(
			'foo title',
			(new Wiki\MySqlDocument(
				1,
				$this->preparedDocuments()
			))->title()
		);
	}

	public function testDate() {
		Assert::equal(
			new \Datetime('2000-01-01 01:01:01'),
			(new Wiki\MySqlDocument(
				1,
				$this->preparedDocuments()
			))->date()
		);
	}

	public function testAuthor() {
		$this->preparedUsers();
		$this->preparedDocuments();
		Assert::equal(
			new Access\MySqlIdentity(1, $this->connection()),
			(new Wiki\MySqlDocument(
				1,
				$this->connection()
			))->author()
		);
	}

	public function testSource() {
		$connection = $this->preparedInformationSources();
		Assert::equal(
			new Wiki\MySqlInformationSource(1, $connection),
			(new Wiki\MySqlDocument(
				1,
				$connection
			))->source()
		);
	}


	public function testId() {
		Assert::same(
			1,
			(new Wiki\MySqlDocument(1, new Fake\Database))->id()
		);
	}

	public function testEditing() {
		$connection = $this->preparedDocuments();
		(new Wiki\MySqlDocument(1, $connection))
		->edit('newTitle', 'newDescription');
		Assert::same(
			1,
			$connection->fetchColumn('SELECT COUNT(ID) FROM documents')
		);
		Assert::same(
			[
				'title' => 'newTitle',
				'description' => 'newDescription',
				'created_at' => '2000-01-01 01:01:01'
			],
			$connection->fetch(
				'SELECT title, description, created_at FROM documents'
			)
		);
	}

	private function preparedDocuments() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		$connection->query(
			'INSERT INTO documents
			(ID, user_id, created_at, description, information_source_id, title)
			VALUES (1, 1, "2000-01-01 01:01:01", "foo description", 1, "foo title")'
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


(new MySqlDocument())->run();
