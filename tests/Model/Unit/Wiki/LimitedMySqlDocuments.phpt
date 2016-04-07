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

final class LimitedMySqlDocuments extends TestCase\Database {
    public function testIteratingInRange() {
        $connection = $this->preparedDatabase();
        $pagination = new \Nette\Utils\Paginator;
        $pagination->itemsPerPage = 1;
        $pagination->page = 1;
        $documents = new Wiki\LimitedMySqlDocuments(
            $connection,
            new Fake\Documents,
            $pagination
        );
        $pagination->itemCount = 2;
        Assert::same(2, $documents->count());
        $rows = $documents->iterate();
        Assert::equal(
            new Wiki\ConstantDocument(
                'secondTitle',
                'second',
                new Access\MySqlIdentity(1, $connection),
                new \Datetime('2000-01-01 01:01:01'),
                new Wiki\MySqlInformationSource(1, $connection),
                new Wiki\MySqlDocument(2, $connection)
            ),
            $rows->current()
        );
        $rows->next();
        Assert::false($rows->valid());
    }

    public function testOutOfRangeOffset() {
        $connection = $this->preparedDatabase();
        $pagination = $this->mockery('Nette\Utils\Paginator');
        $pagination->shouldReceive('getOffset')->andReturn(100)->once();
        $pagination->shouldReceive('getLength')->andReturn(2)->once();
        $documents = new Wiki\LimitedMySqlDocuments(
            $connection,
            new Fake\Documents,
            $pagination
        );
        Assert::same(2, $documents->count());
        $rows = $documents->iterate();
        Assert::false($rows->valid());
    }

    private function preparedDatabase() {
        $connection = $this->connection();
        $connection->query('TRUNCATE documents');
        $connection->query(
            'INSERT INTO documents
			(ID, user_id, created_at, description, information_source_id, title)
			VALUES
			(2, 1, "2000-01-01 01:01:01", "second", 1, "secondTitle"),
			(1, 2, "1999-01-01 01:01:01", "first", 1, "firstTitle")'
        );
        return $connection;
    }
}


(new LimitedMySqlDocuments())->run();
