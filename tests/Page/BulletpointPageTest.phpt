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

final class BulletpointPageTest extends TestCase\Page {
    public function testAddingWithNotEnoughPermission() {
        $this->checkRedirect(
            'Bulletpoint:pridat',
            '/prihlasit',
            ['slug' => 'php-programovaci-jazyk']
        );
    }

    public function testAdding() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction(
            'Bulletpoint:pridat',
            ['slug' => 'php-programovaci-jazyk']
        );
    }

    /**
     * @throws \Nette\Application\BadRequestException Bulletpoint neexistuje
     */
    public function testEditingUnknownComment() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Bulletpoint:upravit', ['id' => 999]);
    }

    public function testEditingWithNotEnoughPermission() {
        $this->checkRedirect(
            'Bulletpoint:upravit',
            '/prihlasit',
            ['id' => 3]
        );
    }

    public function testEditing() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $response = $this->checkAction('Bulletpoint:upravit', ['id' => 1]);
        $html = Tester\DomQuery::fromHtml((string)$response->getSource());
        Assert::true($html->has('input[value="Jednoduchost"]'));
    }
}


(new BulletpointPageTest($container))->run();