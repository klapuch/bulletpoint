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

final class NavrhyPageTest extends TestCase\Page {
    public function testDefaultWithForbiddenAccess() {
        $this->checkRedirect('Navrhy:default', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testDefaultWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:default');
    }

    public function testDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:default');
    }

    public function testDocumentsWithForbiddenAccess() {
        $this->checkRedirect('Navrhy:dokumenty', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testDocumentsWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:dokumenty');
    }

    public function testDocuments() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:dokumenty');
    }

    public function testBulletpointsWithForbiddenAccess() {
        $this->checkRedirect('Navrhy:bulletpointy', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testBulletpointsWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:bulletpointy');
    }

    public function testBulletpoints() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:bulletpointy');
    }
}


(new NavrhyPageTest($container))->run();