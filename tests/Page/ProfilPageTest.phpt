<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class ProfilPageTest extends TestCase\Page {
    public function testDefault() {
        $this->checkAction('Profil:default', ['username' => 'facedown']);
    }

    /**
     * @throws \Nette\Application\BadRequestException Uživatel neexistuje
     */
    public function testUnknownUsername() {
        $this->checkAction('Profil:default', ['username' => 'foooo']);
    }
}


(new ProfilPageTest($container))->run();