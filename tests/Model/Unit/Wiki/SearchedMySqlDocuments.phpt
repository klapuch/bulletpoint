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

final class SearchedMySqlDocuments extends TestCase\Database {
	public function testResults() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("Škoda", "fooDescription")'
		);
		$documents = new Wiki\SearchedMySqlDocuments(
            'skod',
            $connection,
            new Fake\Documents
        );
        Assert::same(1, $documents->count());
        $iterators = $documents->iterate();
        $document = $iterators->current();
		Assert::same('Škoda', $document->title());
		Assert::same('fooDescription', $document->description());
        $iterators->next();
        Assert::false($iterators->valid());
	}

	public function testNoMatch() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("The Sound Of Perseverance", "fooDescription")'
		);
		$documents = new Wiki\SearchedMySqlDocuments(
            'fooooo',
            $connection,
            new Fake\Documents
        );
        Assert::same(0, $documents->count());
		Assert::false($documents->iterate()->valid());
	}

	public function testEllipsis() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("fooTitle", REPEAT("0123456789", 11))'
		);
		$documents = new Wiki\SearchedMySqlDocuments(
            'foo',
            $connection,
            new Fake\Documents
        );
        Assert::same(1, $documents->count());
        $iterators = $documents->iterate();
		Assert::same(
			str_repeat('0123456789', 10) . '...',
			$iterators->current()->description()
		);
        $iterators->next();
        Assert::false($iterators->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		return $connection;
	}
}


(new SearchedMySqlDocuments())->run();
