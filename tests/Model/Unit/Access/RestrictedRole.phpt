<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Access;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class RestrictedRole extends \Tester\TestCase {
    public function testRestrictionForLowerRank() {
        $role = new Access\RestrictedRole(
            new Fake\Identity(2, new Fake\Role('member', 1)),
            new Fake\Role('administrator', 2)
        );
        Assert::exception(
            function() use ($role) {
                $role->degrade();
            },
            'Bulletpoint\Exception\AccessDeniedException',
            'Nedostatečná role pro změnu'
        );
        Assert::exception(
            function() use ($role) {
                $role->promote();
            },
            'Bulletpoint\Exception\AccessDeniedException',
            'Nedostatečná role pro změnu'
        );
    }

    public function testRestrictionForSameRank() {
        $myRole = new Fake\Role('administrator', 2);
        $role = new Access\RestrictedRole(
            new Fake\Identity(2, $myRole),
            new Fake\Role('administrator', 2)
        );
        Assert::same($myRole->rank(), $role->rank()); //must be same for test
        Assert::exception(
            function() use ($role) {
                $role->degrade();
            },
            'Bulletpoint\Exception\AccessDeniedException',
            'Nedostatečná role pro změnu'
        );
        Assert::exception(
            function() use ($role) {
                $role->promote();
            },
            'Bulletpoint\Exception\AccessDeniedException',
            'Nedostatečná role pro změnu'
        );
    }

    public function testNoRestriction() {
        $role = new Access\RestrictedRole(
            new Fake\Identity(2, new Fake\Role('creator', 3)),
            new Fake\Role('administrator', 2)
        );
        $role->promote();
        $role->degrade();
        Assert::true(true);
    }
}


(new RestrictedRole())->run();
