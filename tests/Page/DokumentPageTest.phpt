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

final class DokumentPageTest extends TestCase\Page {
    public function testRenderDefault() {
        $this->checkAction('Dokument:default', ['slug' => 'php-programovaci-jazyk']);
    }

    /**
     * @throws \Nette\Application\BadRequestException Dokument neexistuje
     */
    public function testUnknownDocument() {
        $this->checkAction('Dokument:default', ['slug' => 'fooo']);
    }

    public function testRenderNovyOnLoggedInUser() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Dokument:novy');
    }

    public function testRenderNovyOnLoggedOutUser() {
        $this->checkRedirect('Dokument:novy', '/prihlasit');
    }

    /**
     * @throws \Nette\Application\BadRequestException Dokument neexistuje
     */
    public function testUnknownDocumentWithRenderUpravit() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Dokument:upravit', ['slug' => 'fooo']);
    }

    public function testRenderUpravitOnLoggedInUser() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $this->checkAction('Dokument:upravit', ['slug' => 'php-programovaci-jazyk']);
    }

    public function testRenderUpravitOnLoggedOutUser() {
        $this->checkRedirect(
            'Dokument:upravit',
            '/prihlasit',
            ['slug' => 'php-programovaci-jazyk']
        );
    }

    public function testDefaultValues() {
        $this->logIn(1, ['creator'], ['username' => 'facedown']);
        $response = $this->checkAction('Dokument:upravit', ['slug' => 'automobil-skoda-auto']);
        $html = Tester\DomQuery::fromHtml((string)$response->getSource());
        Assert::true($html->has('input[value="Automobil Škoda auto"]'));
        Assert::same('Just Škoda', (string)$html->find('textarea')[0]);
    }
}


(new DokumentPageTest($container))->run();