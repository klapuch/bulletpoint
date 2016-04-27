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

final class ProchazetPageTest extends TestCase\Page {
    public function testDocuments() {
        $this->checkAction('Prochazet:dokumenty');
    }
}


(new ProchazetPageTest($container))->run();