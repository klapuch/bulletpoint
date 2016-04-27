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

final class TrestyPageTest extends TestCase\Page {
    public function testDefaultWithForbiddenAccess() {
        $this->checkRedirect('Tresty:default', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testDefaultWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Tresty:default');
    }

    public function testDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Tresty:default');
    }
}


(new TrestyPageTest($container))->run();