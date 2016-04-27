<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Component;

use Tester;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

$container = require __DIR__ . '/../bootstrap.php';

final class RoleTest extends TestCase\Component {
    public function testMyself() {
        $this->logIn(1, ['creator']);
        $role = new Role(
            new Fake\Identity(1, new Fake\Role('creator', 3)),
            new Fake\Identity(1, new Fake\Role('creator', 3))
        );
        $this->checkRenderOutput($role, '<h6>Tvůrce</h6>');
    }

    public function testMyselfWithLowerRole() {
        $this->logIn(2, ['guest']);
        $role = new Role(
            new Fake\Identity(1, new Fake\Role('creator', 3)),
            new Fake\Identity(2, new Fake\Role('member', 1))
        );
        $this->checkRenderOutput($role, '<h6>Tvůrce</h6>');
    }

    public function testMyselfWithHigherRole() {
        $this->logIn(1, ['creator']);
        $role = new Role(
            new Fake\Identity(2, new Fake\Role('member', 1)),
            new Fake\Identity(1, new Fake\Role('creator', 3))
        );
        $this->checkRenderOutput($role, __DIR__ . '/expected/Role-myselfWithHigherRole.expected');
    }
}


(new RoleTest($container))->run();