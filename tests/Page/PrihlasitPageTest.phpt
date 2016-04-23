<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Nette\Security;
use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class PrihlasitPageTest extends TestCase\Page {
    public function testRenderDefault() {
        $this->checkAction('Prihlasit:default');
    }

    /**
     * @throws \Nette\Application\BadRequestException Přihlášení pro člena neexistuje
     */
    public function testAlreadyLoggedInUser() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Prihlasit:default');
    }

    public function testSuccessfulLogin() {
        $this->checkForm(
            'Prihlasit:default',
            'loginForm-form',
            ['username' => 'facedown', 'password' => 'facedown']
        );
        Assert::equal(
            new Security\Identity(1, ['creator'], ['username' => 'facedown']),
            $this->getPresenter()->user->identity
        );
    }
    
    public function testPunishedUser() {
        $this->checkForm(
            'Prihlasit:default',
            'loginForm-form',
            ['username' => 'banned', 'password' => 'facedown']
        );
        Assert::false($this->getPresenter()->user->loggedIn);
    }
}


(new PrihlasitPageTest($container))->run();