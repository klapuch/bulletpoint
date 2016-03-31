<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Report;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class MySqlTargets extends TestCase\Database {
	public function testIterating() {
		$connection = $this->preparedDatabase();
		$connection->query(
			'INSERT INTO comment_complaints (comment_id, settled, reason, complained_at)
			VALUES
			(1, 0, "Jiné", NOW()),
			(1, 0, "Jiné", NOW()),
			(1, 0, "Spam", NOW()),
			(2, 0, "Vulgarita", NOW() - INTERVAL 1 DAY),
			(3, 0, "Vulgarita", NOW())'
		);
		$rows = (new Report\MySqlTargets($connection))->iterate();
        Assert::equal(
            ['total' => 3,'reason' => 'Jiné', 'target' => new Report\Target(1)],
            $rows->current()
        );
        $rows->next();
        Assert::equal(
            ['total' => 1, 'reason' => 'Spam', 'target' => new Report\Target(1)],
            $rows->current()
        );
        $rows->next();
        Assert::equal(
            ['total' => 1, 'reason' => 'Vulgarita', 'target' => new Report\Target(2)],
            $rows->current()
        );
        $rows->next();
        Assert::false($rows->valid());
	}

	private function preparedDatabase() {
		$connection = $this->connection();
		$connection->query('TRUNCATE comment_complaints');
		$connection->query('TRUNCATE comments');
        $connection->query(
            'INSERT INTO comments (ID, visible)
             VALUES (1, 1), (2, 1), (3, 0)'
        );
		$connection->query(
			'INSERT INTO comment_complaints
			(ID, comment_id, settled, reason, user_id)
			VALUES (1, 1, 0, "Jiné", 1)'
		);
		return $connection;
	}
}


(new MySqlTargets())->run();
