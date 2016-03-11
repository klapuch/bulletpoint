<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Integration;

use Tester;
use Tester\Assert;
use Bulletpoint\Core\Storage;

require __DIR__ . '/../../../bootstrap.php';

final class Transaction extends Tester\TestCase {
	private $database;
	private $transaction;

	public function setUp() {
		Tester\Environment::lock('pdo_test', __DIR__ . '/../../../temp');
		$this->database = new Storage\PDODatabase(
			'127.0.0.1',
			'root',
			'',
			'pdo_test'
		);
		$this->transaction = new Storage\Transaction($this->database);
		$this->database->query('TRUNCATE test');
	}

	public function testSuccessfulTransaction() {
		$lastId = $this->transaction->start(function() {
			$this->database->query('INSERT INTO test (name) VALUES ("foo")');
			$this->database->query('INSERT INTO test (name) VALUES ("foo2")');
			$foo2Id = $this->database->fetchColumn('SELECT LAST_INSERT_ID()');
			$this->database->query('DELETE FROM test WHERE name = "foo2"');
			return $foo2Id;
		});
		Assert::same(2, $lastId);
		Assert::equal(
			[['ID' => 1, 'name' => 'foo']],
			$this->database->fetchAll('SELECT * FROM test')
		);
	}

	public function testForcedPdoException() {
		$exception = Assert::exception(function() {
			$this->transaction->start(function() {
				$this->database->query('INSERT INTO test (name) VALUES ("foo")');
				$this->database->query('INSERT INTO test (name) VALUES ("foo2")');
				$this->database->query('SOMETHING STRANGE TO MYSQL DATABASE!');
			});
		}, 'Bulletpoint\Exception\StorageException', 'Nastala chyba na straně úložiště.', null);
		Assert::type('\PDOException', $exception->getPrevious());
		Assert::equal(
			[],
			$this->database->fetchAll('SELECT * FROM test')
		);
	}

	public function testForcedException() {
		Assert::exception(function() {
			$this->transaction->start(function() {
				$this->database->query('INSERT INTO test (name) VALUES ("foo")');
				$this->database->query('INSERT INTO test (name) VALUES ("foo2")');
				throw new \RuntimeException('Forced exception');
			});
		}, '\RuntimeException', 'Forced exception');
		Assert::equal(
			[],
			$this->database->fetchAll('SELECT * FROM test')
		);
	}
}

(new Transaction())->run();