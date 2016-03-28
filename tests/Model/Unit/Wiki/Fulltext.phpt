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

final class Fulltext extends TestCase\Database {
	public function testResults() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("Škoda", "fooDescription")'
		);
		$matches = (new Wiki\Fulltext($connection))->matches('skod');
		Assert::same(1, key($matches));
		Assert::same('Škoda', $matches[1][0]['title']);
		Assert::same('fooDescription', $matches[1][0]['description']);
		Assert::same('sl-ug', $matches[1][0]['slug']);
	}

	public function testNoMatch() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("The Sound Of Perseverance", "fooDescription")'
		);
		$matches = (new Wiki\Fulltext($connection))->matches('fooooo');
		Assert::same(0, key($matches));
	}

	public function testLookingForEmptyKeyword() {
		$connection = $this->preparedDatabase();
		$matches = (new Wiki\Fulltext($connection))->matches('');
		Assert::same(0, key($matches));
	}

	public function testEllipsis() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO documents (title, description)
			VALUES ("fooTitle", REPEAT("0123456789", 11))'
		);
		$matches = (new Wiki\Fulltext($connection))->matches('foo');
		Assert::same(
			str_repeat('0123456789', 10) . '...',
			$matches[1][0]['description']
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE documents');
		$connection->query('TRUNCATE document_slugs');
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (1, "sl-ug")'
		);
		return $connection;
	}
}


(new Fulltext())->run();
