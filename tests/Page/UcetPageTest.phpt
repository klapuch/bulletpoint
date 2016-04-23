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
    public function testRenderDefaultForGuest() {
        $this->checkRedirect('Ucet:default', '/prihlasit');
    }

    /*public function testRenderDefaultForMember() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        Assert::true((string)$this->checkAction('Ucet:default')->getSource());
    }*/
}


(new UcetPageTest($container))->run();