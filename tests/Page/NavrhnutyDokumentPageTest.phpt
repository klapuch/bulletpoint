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

final class NavrhnutyDokumentPageTest extends TestCase\Page {
    public function testDefaultWithForbiddenAccess() {
        $this->checkRedirect(
            'NavrhnutyDokument:default',
            '/prihlasit',
            ['id' => 4]
        );
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testDefaultWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('NavrhnutyDokument:default', ['id' => 4]);
    }

    public function testDefault() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('NavrhnutyDokument:default', ['id' => 4]);
    }

    /**
     * @throws \Nette\Application\BadRequestException Návrh neexistuje
     */
    public function testUnknownProposal() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('NavrhnutyDokument:default', ['id' => 99]);
    }

    public function testEditingWithForbiddenAccess() {
        $this->checkRedirect(
            'NavrhnutyDokument:upravit',
            '/prihlasit',
            ['id' => 4]
        );
    }

    /**
     * @throws \Nette\Application\BadRequestException Na tuto stránku nemáte dostatečné oprávnění
     */
    public function testEditingWithNotEnoughPermission() {
        $this->logIn(3, ['member'], ['username' => 'test2']);
        $this->checkAction('NavrhnutyDokument:upravit', ['id' => 4]);
    }

    /**
     * @throws \Nette\Application\BadRequestException Návrh neexistuje
     */
    public function testEditingUnknownProposal() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('NavrhnutyDokument:upravit', ['id' => 99]);
    }

    public function testEditing() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $response = $this->checkAction('NavrhnutyDokument:upravit', ['id' => 4]);
        $html = Tester\DomQuery::fromHtml((string)$response->getSource());
        Assert::true($html->has('input[value="Automobil BMW"]'));
        Assert::same('Just BMW', (string)$html->find('textarea')[0]);
    }
}


(new NavrhnutyDokumentPageTest($container))->run();