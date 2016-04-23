<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class RegistracePageTest extends TestCase\Page {
    /**
     * @throws \Nette\Application\BadRequestException Registrace pro člena neexistuje
     */
    public function testRegistrationForLoggedInUser() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Registrace:default');
    }
}


(new RegistracePageTest($container))->run();