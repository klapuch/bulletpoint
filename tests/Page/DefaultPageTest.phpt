<?php

/**
 * @testCase
 * @phpVersion > 7.0.0
 */

namespace Bulletpoint\Page;

use Tester;
use Tester\Assert;
use Bulletpoint\Model\Access;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../testbenchBootstrap.php';

final class DefaultPageTest extends Tester\TestCase {
    use \Testbench\TPresenter;

    public function testRenderDefault() {
        $this->checkAction('Default:default');
    }
}


(new DefaultPageTest())->run();