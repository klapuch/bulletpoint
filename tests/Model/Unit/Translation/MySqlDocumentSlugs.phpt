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
		$slug = (new Translation\MySqlDocumentSlugs($connection))
            ->add(1, 'hey hou');
		Assert::equal(new Translation\MySqlDocumentSlug('hey hou', $connection), $slug);
		Assert::same(
			['ID' => 1, 'origin' => 1, 'slug' => 'hey hou'],
			$connection->fetch(
				'SELECT ID, origin, slug FROM document_slugs'
			)
		);
	}

	/**
	* @throws \Bulletpoint\Exception\DuplicateException Slug "hey-hou" již existuje
	*/
	public function testAddingDuplication() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (5, "hey-hou")'
		);
		(new Translation\MySqlDocumentSlugs($connection))
            ->add(3, 'hey-hou');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_slugs');
		return $connection;
	}
}


(new MySqlDocumentSlugs())->run();
