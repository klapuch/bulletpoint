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

final class AllMySqlDocuments extends TestCase\Database {
    public function testIterating() {
        $connection = $this->preparedDatabase();
        $documents = new Wiki\AllMySqlDocuments(
            $connection,
            new Fake\Documents
        );
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
        Assert::equal(
            new Wiki\ConstantDocument(
                'firstTitle',
                'first',
                new Access\MySqlIdentity(2, $connection),
                new \Datetime('1999-01-01 01:01:01'),
                new Wiki\MySqlInformationSource(1, $connection),
                new Wiki\MySqlDocument(1, $connection)
            ),
            $rows->current()
        );
        $rows->next();
        Assert::false($rows->valid());
        Assert::same(2, $documents->count());
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


(new AllMySqlDocuments())->run();
