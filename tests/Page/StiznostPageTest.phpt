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

final class StiznostPageTest extends TestCase\Page {
    /**
     * @throws \Nette\Application\BadRequestException Komentář neexistuje
     */
    public function testUnknownComplaint() {
        $this->logIn(0, ['creator'], ['username' => 'noone']);
        $this->checkAction('Stiznost:default', ['id' => 999]);
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testNotEnoughPermissionToRenderDefault() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Stiznost:default', ['id' => 1]);
    }

    public function testForbiddenAccessToRenderDefault() {
        $this->checkRedirect('Stiznost:default', '/prihlasit', ['id' => 1]);
    }

    public function testRenderDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Stiznost:default', ['id' => 1]);
    }

    public function testRenderDefaultWithNoMoreComplaints() {
        $this->container
            ->getByType('Bulletpoint\Model\Storage\Database')
            ->query('TRUNCATE comment_complaints');
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkRedirect('Stiznost:default', '/stiznosti', ['id' => 1]);
    }
}


(new StiznostPageTest($container))->run();