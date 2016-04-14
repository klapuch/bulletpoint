<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\Model\Wiki;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class ReversedBulletpoints extends Tester\TestCase {
    public function testReverting() {
        Assert::equal(
            [
                new Fake\Bulletpoint(3),
                new Fake\Bulletpoint(2),
                new Fake\Bulletpoint(1),
            ],
            (new Wiki\ReversedBulletpoints(new Fake\Bulletpoints([1, 2, 3])))
            ->iterate()
        );
    }
}


(new ReversedBulletpoints())->run();
