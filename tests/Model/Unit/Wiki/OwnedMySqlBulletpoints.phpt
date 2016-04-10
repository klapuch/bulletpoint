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

final class OwnedMySqlBulletpoints extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
        $owner = new Fake\Identity(2);
		$rows = (new Wiki\OwnedMySqlBulletpoints(
			$owner,
			$connection
		))->iterate();
		Assert::equal(
			new Wiki\ConstantBulletpoint(
				$owner,
				'second',
				new \Datetime('1999-01-01 01:01:01'),
                new Wiki\MySqlInformationSource(2, $connection),
				new Wiki\MySqlBulletpoint(2, $connection),
                new Wiki\ConstantDocument(
                    'fooTitle',
                    'fooDescription',
                    new Access\MySqlIdentity(666, $connection),
                    new \DateTime('2000-01-01'),
                    new Wiki\MySqlInformationSource(
                        100,
                        $connection
                    ),
                    new Wiki\MySqlDocument(9, $connection)
                )
			),
			$rows->current()
		);
		$rows->next();
		Assert::false($rows->valid());
	}

    public function testAdding() {
        $connection = $this->preparedDatabase();
        (new Wiki\OwnedMySqlBulletpoints(
            new Fake\Identity(4),
            $connection
        ))->add(
            'new content',
            new Fake\Document(1),
            new Fake\InformationSource(1, 'wikipeide', 2005, 'facedown')
        );
        Assert::same(
            [
                'user_id' => 4,
                'information_source_id' => 1,
                'document_id' => 1
            ],
            $connection->fetch(
                'SELECT user_id, information_source_id, document_id
				FROM bulletpoints
				WHERE content = "new content"'
            )
        );
    }

    /**
     * @throws \Bulletpoint\Exception\DuplicateException Bulletpoint již existuje
     */
    public function testAddingExistingOne() {
        $connection = $this->preparedDatabase();
        (new Wiki\OwnedMySqlBulletpoints(
            new Fake\Identity(4),
            $connection
        ))->add(
            'first',
            new Fake\Document(9),
            new Fake\InformationSource(1, 'wikipeide', 2005, 'facedown')
        );
    }

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE bulletpoints');
		$connection->query('TRUNCATE documents');
		$connection->query(
			'INSERT INTO bulletpoints
			(ID, content, user_id, information_source_id, document_id, created_at)
			VALUES
			(1, "first", 1, 1, 9, "2000-01-01 01:01:01"),
			(2, "second", 2, 2, 9, "1999-01-01 01:01:01")'
		);
        $connection->query(
            'INSERT INTO documents
            (ID, title, description, created_at, user_id, information_source_id)
            VALUES (9, "fooTitle", "fooDescription", "2000-01-01", 666, 100)'
        );
		return $connection;
	}
}


(new OwnedMySqlBulletpoints())->run();
