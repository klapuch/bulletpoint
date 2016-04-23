<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class MojePageTest extends TestCase\Page {
    public function testRenderDokumentyAsMember() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Moje:dokumenty');
    }

    public function testRenderBulletpointyAsMember() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Moje:bulletpointy');
    }

    public function testRenderDokumentyAsGuest() {
        $this->checkRedirect('Moje:dokumenty', '/prihlasit');
    }

    public function testRenderBulletpointyAsGuest() {
        $this->checkRedirect('Moje:bulletpointy', '/prihlasit');
    }
}


(new MojePageTest($container))->run();