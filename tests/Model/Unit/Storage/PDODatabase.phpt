<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Integration;

use Tester;
use Tester\Assert;
use Bulletpoint\Model\Storage;

require __DIR__ . '/../../../bootstrap.php';

final class PDODatabase extends Tester\TestCase {
	private $database;

	public function setUp() {
		Tester\Environment::lock('pdo_test', __DIR__ . '/../../../temp');
		$this->database = new Storage\PDODatabase(
			'127.0.0.1',
			'root',
			'',
			'pdo_test'
		);
		$this->database->query('TRUNCATE test');
	}

	/**
	* @throws \RuntimeException Connection to database was not successful
	*/
	public function testWrongCredentials() {
		new Storage\PDODatabase(
			'???????',
			'!!!!!!!',
			',,,,,,,',
			'¨¨¨¨¨¨¨¨'
		);
	}

	public function testQuery() {
		$this->database->exec('START TRANSACTION');
		$this->database->query(
			'INSERT INTO test (ID, name) VALUES (1, ?)',
			['name' => 'foo']
		);
		$this->database->exec('COMMIT');
		$this->database->exec('START TRANSACTION');
		$this->database->query(
			'INSERT INTO test (ID, name) VALUES (2, ?)',
			['kůň a míček']
		);
		$rows = $this->database->query('SELECT * FROM test');
		Assert::equal(
			[['ID' => 1, 'name' => 'foo'], ['ID' => 2, 'name' => 'kůň a míček']],
			$rows->fetchAll(\PDO::FETCH_ASSOC)
		);
		$this->database->exec('ROLLBACK');
		$rows = $this->database->query('SELECT * FROM test');
		Assert::equal(
			[['ID' => 1, 'name' => 'foo']],
			$rows->fetchAll(\PDO::FETCH_ASSOC)
		);
        Assert::type(
            'Traversable',
            $this->database->query('SELECT * FROM test')
        );
	}

	public function testFetching() {
		$this->database->query(
			'INSERT INTO test (ID, name) VALUES (?, ?)',
			[5, 'foo']
		);
		Assert::equal(
			['ID' => 5, 'name' => 'foo'],
			$this->database->fetch(
				'SELECT * FROM test WHERE ID = ? LIMIT ?, ?',
				[5, 0, 10] // test \PDO::ATTR_EMULATE_PREPARES => false
			)
		);
	}

	public function testFetchingAll() {
		$this->database->query(
			'INSERT INTO test (ID, name) VALUES (?, ?)',
			[5, 'foo']
		);
		$rows = $this->database->fetchAll('SELECT * FROM test');
		Assert::equal(5, $rows[0]['ID']);
		Assert::equal('foo', $rows[0]['name']);
	}

	public function testFetchingColumn() {
		$this->database->query(
			'INSERT INTO test (ID, name) VALUES (?, ?)',
			[5, 'foo']
		);
		$name = $this->database->fetchColumn('SELECT name FROM test WHERE ID = ?', [5]);
		Assert::equal('foo', $name);
	}
}

(new PDODatabase())->run();