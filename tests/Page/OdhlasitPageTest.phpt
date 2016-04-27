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

final class OdhlasitPageTest extends TestCase\Page {
    public function testLoggingOut() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkRedirect('Odhlasit:default', '/');
        Assert::false($this->getPresenter()->user->loggedIn);
    }

    /**
     * @throws \Nette\Application\BadRequestException Odhlášení pro hosta neexistuje
     */
    public function testLogginOutGuest() {
        $this->checkAction('Odhlasit:default');
    }
}


(new OdhlasitPageTest($container))->run();