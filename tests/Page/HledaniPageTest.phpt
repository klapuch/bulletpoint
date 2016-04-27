<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Bulletpoint\TestCase;

$container = require __DIR__ . '/../bootstrap.php';

final class HledaniPageTest extends TestCase\Page {
    public function testExactResult() {
        $this->checkRedirect(
            'Hledani:default',
            '/dokument/php-programovaci-jazyk',
            ['keyword' => 'PHP programovací jazyk']
        );
    }

    public function testNoResults() {
        $this->checkAction(
            'Hledani:default',
            ['keyword' => 'xxxxxxxxxxxxxxxxx']
        );
    }

    public function testMoreResults() {
        $this->checkAction(
            'Hledani:default',
            ['keyword' => 'automobil']
        );
    }
}


(new HledaniPageTest($container))->run();