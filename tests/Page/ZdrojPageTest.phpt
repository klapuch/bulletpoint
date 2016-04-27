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

final class ZdrojPageTest extends TestCase\Page {
    /**
     * @throws \Nette\Application\BadRequestException Zdroj neexistuje
     */
    public function testUnknownSource() {
        $this->logIn(0, ['creator'], ['username' => 'noone']);
        $this->checkAction('Zdroj:upravit', ['id' => 999]);
    }

    public function testEditing() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $response = $this->checkAction('Zdroj:upravit', ['id' => 6]);
        $html = Tester\DomQuery::fromHtml((string)$response->getSource());
        Assert::true($html->has('input[value="Wonderland"]'));
        Assert::true($html->has('input[value="Alice"]'));
        Assert::true($html->has('input[value="2009"]'));
    }
}


(new ZdrojPageTest($container))->run();