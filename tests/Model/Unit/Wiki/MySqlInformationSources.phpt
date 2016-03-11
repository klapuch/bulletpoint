<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Wiki;
use Bulletpoint\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlInformationSources extends TestCase\Database {
	public function testAddingExistingSource() {
		$connection = $this->preparedDatabase();
		Assert::equal(
			new Wiki\ConstantInformationSource(
				'wikipedie',
				2005,
				'facedown',
				new Wiki\MySqlInformationSource(2, $connection)
			),
			(new Wiki\MySqlInformationSources($connection))
			->create('wikipedie', 2005, 'facedown'));
	}

	public function testAddingNewSource() {
		$connection = $this->preparedDatabase();
		Assert::equal(
			new Wiki\ConstantInformationSource(
				'book',
				1888,
				'facedown',
				new Wiki\MySqlInformationSource(2, $connection)
			),
			(new Wiki\MySqlInformationSources($connection))
			->create('book', 1888, 'facedown'));
	}

	public function testAddingNewSourceWithEmptyYear() {
		$connection = $this->preparedDatabase();
		Assert::equal(
			new Wiki\ConstantInformationSource(
				'xxx',
				null,
				'facedown',
				new Wiki\MySqlInformationSource(2, $connection)
			),
			(new Wiki\MySqlInformationSources($connection))
			->create('xxx', '', 'facedown'));
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE information_sources');
		$connection->query(
			'INSERT INTO information_sources
			(ID, place, author, `year`)
			VALUES (1, "wikipedie", "facedown", 2005)'
		);
		return $connection;
	}
}


(new MySqlInformationSources())->run();
