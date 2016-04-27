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

final class KomentarPageTest extends TestCase\Page {
    /**
     * @throws \Nette\Application\BadRequestException Komentář neexistuje
     */
    public function testEditingUnknownComment() {
        $this->logIn(0, ['creator'], ['username' => 'noone']);
        $this->checkAction('Komentar:upravit', ['id' => 999]);
    }

    /**
     * @throws \Nette\Application\BadRequestException Komentář nemůžeš upravovat
     */
    public function testEditingForeignComment() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('Komentar:upravit', ['id' => 1]);
    }

    /**
     * @throws \Nette\Application\BadRequestException Komentář nemůžeš upravovat
     */
    public function testEditingInvisibleComment() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Komentar:upravit', ['id' => 2]);
    }

    public function testEditing() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $response = $this->checkAction('Komentar:upravit', ['id' => 1]);
        $html = Tester\DomQuery::fromHtml((string)$response->getSource());
        Assert::same('Best comment I have ever written', (string)$html->find('textarea')[0]);
    }
}


(new KomentarPageTest($container))->run();