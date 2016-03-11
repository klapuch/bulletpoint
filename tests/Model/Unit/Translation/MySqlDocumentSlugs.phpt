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

final class MySqlDocumentSlugs extends TestCase\Database {
	public function testAdding() {
		$connection = $this->preparedDatabase();
		(new Translation\MySqlDocumentSlugs($connection, new Fake\Correction))
		->add(1, 'sl-ug');
		Assert::same(
			['ID' => 1, 'origin' => 1, 'slug' => 'sl-ug'],
			$connection->fetch(
				'SELECT ID, origin, slug FROM document_slugs'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Slug "sl-ug" již existuje
	*/
	public function testAddingDuplication() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (5, "sl-ug")'
		);
		(new Translation\MySqlDocumentSlugs($connection, new Fake\Correction))
		->add(3, 'sl-ug');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_slugs');
		return $connection;
	}
}


(new MySqlDocumentSlugs())->run();
