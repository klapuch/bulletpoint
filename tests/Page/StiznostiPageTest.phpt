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

final class StiznostiPageTest extends TestCase\Page {
    public function testDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Stiznosti:default');
    }

    public function testDefaultWithForbiddenAccess() {
        $this->checkRedirect('Stiznosti:default', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testDefaultWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Stiznosti:default');
    }
}


(new StiznostiPageTest($container))->run();