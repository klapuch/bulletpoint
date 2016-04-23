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
    public function testForbiddenAccessToRenderDefault() {
        $this->checkRedirect('Navrhy:default', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testNotEnoughPermissionToRenderDefault() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:default');
    }

    public function testRenderDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:default');
    }

    public function testForbiddenAccessToRenderDokumenty() {
        $this->checkRedirect('Navrhy:dokumenty', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testNotEnoughPermissionToRenderDokumenty() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:dokumenty');
    }

    public function testRenderDokumenty() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:dokumenty');
    }

    public function testForbiddenAccessToRenderBulletpointy() {
        $this->checkRedirect('Navrhy:bulletpointy', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testNotEnoughPermissionToRenderBulletpointy() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Navrhy:bulletpointy');
    }

    public function testRenderBulletpointy() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Navrhy:bulletpointy');
    }
}


(new NavrhyPageTest($container))->run();