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

final class DefaultPageTest extends TestCase\Page {
    public function testDefault() {
        $this->checkAction('Default:default');
    }

    public function testSearchingForm() {
        Tester\Environment::skip('CSRF token is not faked');
        $response = $this->checkForm(
            'Default:default',
            'searchForm',
            ['keyword' => 'php']
        );
        Assert::contains('?keyword=php', $response->url);
    }
}


(new DefaultPageTest($container))->run();