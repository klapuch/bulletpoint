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

final class MySqlInformationSource extends TestCase\Database {
	public function testAuthor() {
		Assert::same(
			'facedown',
			(new Wiki\MySqlInformationSource(
				1,
				$this->preparedInformationSources()
			))->author()
		);
	}

	public function testYear() {
		Assert::same(
			2005,
			(new Wiki\MySqlInformationSource(
				1,
				$this->preparedInformationSources()
			))->year()
		);
	}

	public function testPlace() {
		Assert::same(
			'wikipedie',
			(new Wiki\MySqlInformationSource(
				1,
				$this->preparedInformationSources()
			))->place()
		);
	}

	public function testEditing() {
		$connection = $this->preparedInformationSources();
		(new Wiki\MySqlInformationSource(1, $connection))->edit('a', 2000, 'b');
		Assert::same(
			[
				'place' => 'a',
				'year' => 2000,
				'author' => 'b',
			],
			$connection->fetch(
				'SELECT place, `year`, author
				FROM information_sources
				WHERE ID = 1'
			)
		);
        (new Wiki\MySqlInformationSource(1, $connection))->edit('a', 20, 'b');
        Assert::same(
            [
                'place' => 'a',
                'year' => 20,
                'author' => 'b',
            ],
            $connection->fetch(
                'SELECT place, `year`, author
				FROM information_sources
				WHERE ID = 1'
            )
        );
	}

	public function testEditingWithEmptyYear() {
		$connection = $this->preparedInformationSources();
		(new Wiki\MySqlInformationSource(1, $connection))->edit('a', '', 'b');
		Assert::same(
			[
				'place' => 'a',
				'year' => null,
				'author' => 'b',
			],
			$connection->fetch(
				'SELECT place, `year`, author
				FROM information_sources
				WHERE ID = 1'
			)
		);
	}

	private function preparedInformationSources() {
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


(new MySqlInformationSource())->run();
