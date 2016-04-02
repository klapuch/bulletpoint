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

require __DIR__ . '/../../../bootstrap.php';

final class ConstantBulletpoint extends Tester\TestCase {
    public function testWithDocument() {
        $bulletpoint = new Wiki\ConstantBulletpoint(
            new Fake\Identity(1),
            'foo',
            new \DateTime,
            new Fake\InformationSource(),
            new Fake\Bulletpoint(),
            new Fake\Document(10, 'bar')
        );
        Assert::equal(new Fake\Document(10, 'bar'), $bulletpoint->document());
    }

    public function testWithoutDocument() {
        $bulletpoint = new Wiki\ConstantBulletpoint(
            new Fake\Identity(1),
            'foo',
            new \DateTime,
            new Fake\InformationSource(),
            new Fake\Bulletpoint(100, new Fake\Document(10, 'bar'))
        );
        Assert::equal(new Fake\Document(10, 'bar'), $bulletpoint->document());
    }
}


(new ConstantBulletpoint())->run();
