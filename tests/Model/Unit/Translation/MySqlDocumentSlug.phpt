<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Translation;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlDocumentSlug extends TestCase\Database {
	public function testStraightOrigin() {
		Assert::same(
			10,
			(new Translation\MySqlDocumentSlug(10, new Fake\Database))
			->origin()
		);
	}

	public function testStraightStringSlug() {
		Assert::same(
			'sl-ug',
			(string)new Translation\MySqlDocumentSlug('sl-ug', new Fake\Database)
		);
	}

	public function testOrigin() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (9, "sl-ug")'
		);
		Assert::same(
			'sl-ug',
			(string)new Translation\MySqlDocumentSlug(9, $connection)
		);
		Assert::same(
			9,
			(new Translation\MySqlDocumentSlug('sl-ug', $connection))->origin()
		);
	}

	public function testRenaming() {
		$database = new Fake\Database($fetch = false);
		$slug = new Translation\MySqlDocumentSlug(9, $database);
		Assert::equal( // no restriction for slug
			new Translation\MySqlDocumentSlug('abc cd', $database),
			$slug->rename('abc cd')
		);
		Assert::equal(
			new Translation\MySqlDocumentSlug('abc-abc-ab3', $database),
			$slug->rename('abc-abc-ab3')
		);
		Assert::equal(
			new Translation\MySqlDocumentSlug('abc-abc3_abc', $database),
			$slug->rename('abc-abc3_abc')
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Slug "sl-ug" již existuje
	*/
	public function testRenamingToExistingOne() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (9, "sl-ug")'
		);
		(new Translation\MySqlDocumentSlug(10, $connection))
		->rename('sl-ug');
	}

	public function testValidRenaming() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug)
			VALUES (9, "sl-ug"), (10, "blabla")'
		);
		$firstChange = (new Translation\MySqlDocumentSlug(9, $connection))
		->rename('sl-ug');
		$secondChange = (new Translation\MySqlDocumentSlug(10, $connection))
		->rename('foo');
		Assert::equal($firstChange, new Translation\MySqlDocumentSlug('sl-ug', $connection));
		Assert::equal($secondChange, new Translation\MySqlDocumentSlug('foo', $connection));
		Assert::equal(
			$connection->fetchAll('SELECT origin, slug FROM document_slugs'),
			[['origin' => 9, 'slug' => 'sl-ug'], ['origin' => 10, 'slug' => 'foo']]
		);
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_slugs');
		return $connection;
	}
}


(new MySqlDocumentSlug())->run();
