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

final class AktivacePageTest extends TestCase\Page {
    public function testWrongFormat() {
        $this->checkRedirect(
            'Aktivace:aktivovat',
            '/prihlasit',
            ['code' => 'abcWrongFormat']
        );
    }

    public function testUnknownCode() {
        $this->checkRedirect(
            'Aktivace:aktivovat',
            '/prihlasit',
            ['code' => 'e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363']
        );
    }

    public function testAlreadyUsed() {
        $this->checkRedirect(
            'Aktivace:aktivovat',
            '/prihlasit',
            ['code' => 'eff63c46355bf9bd1cd56fd72b30abf3b6c46c7f3fc22b6bef:8b87701f7b668dd1020f069ad99072a2a165dcbc']
        );
    }

    public function testSuccessfulActivationAndLogin() {
        $this->checkRedirect(
            'Aktivace:aktivovat',
            '/',
            ['code' => '81730cdf7acd80ec74f1027ede147b34c99e15aaa71b048063:c10c76ce17e59c4d304ba37e62d41da353afdd69']
        );
        Assert::equal(
            new Security\Identity(3, ['member'], ['username' => 'test2']),
            $this->getPresenter()->user->identity
        );
    }

    public function testActivationForLoggedInUser() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkRedirect(
            'Aktivace:aktivovat',
            '/',
            ['code' => '81730cdf7acd80ec74f1027ede147b34c99e15aaa71b048063:c10c76ce17e59c4d304ba37e62d41da353afdd69']
        );
    }
}


(new AktivacePageTest($container))->run();