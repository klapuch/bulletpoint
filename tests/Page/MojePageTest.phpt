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
    public function testDocuments() {
        $this->logIn(1, ['member'], ['username' => 'facedown']);
        $this->checkAction('Moje:dokumenty');
    }

    public function testBulletpoints() {
        $this->logIn(1, ['member'], ['username' => 'facedown']);
        $this->checkAction('Moje:bulletpointy');
    }

    public function testDocumentsWithNotEnoughPermission() {
        $this->checkRedirect('Moje:dokumenty', '/prihlasit');
    }

    public function testBulletpointsWithNotEnoughPermission() {
        $this->checkRedirect('Moje:bulletpointy', '/prihlasit');
    }
}


(new MojePageTest($container))->run();