<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class UcetPageTest extends TestCase\Page {
    public function testDefaultWithForbiddenAccess() {
        $this->checkRedirect('Ucet:default', '/prihlasit');
    }

    public function testDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Ucet:default');
    }
}


(new UcetPageTest($container))->run();