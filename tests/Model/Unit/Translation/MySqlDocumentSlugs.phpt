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
		$slug = (new Translation\MySqlDocumentSlugs(
			$connection,
			new Fake\Correction('md5') // check that sl-ug is affected by Correction
		))->add(1, 'sl-ug');
		Assert::equal(new Translation\MySqlDocumentSlug(md5('sl-ug'), $connection), $slug);
		Assert::same(
			['ID' => 1, 'origin' => 1, 'slug' => md5('sl-ug')],
			$connection->fetch(
				'SELECT ID, origin, slug FROM document_slugs'
			)
		);
	}

	/**
	* @throws Bulletpoint\Exception\DuplicateException Slug "107a29d966d588b186ca5ca756da6f83" již existuje
	*/
	public function testAddingDuplication() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO document_slugs (origin, slug) VALUES (5, MD5("sl-ug"))'
		);
		(new Translation\MySqlDocumentSlugs(
			$connection,
			new Fake\Correction('md5') // check that sl-ug is corrected and then checked if exists
		))->add(3, 'sl-ug');
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE document_slugs');
		return $connection;
	}
}


(new MySqlDocumentSlugs())->run();
