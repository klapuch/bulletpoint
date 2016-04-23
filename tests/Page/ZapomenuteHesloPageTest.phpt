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

final class ZapomenuteHesloPageTest extends TestCase\Page {
    /**
     * @throws \Nette\Application\BadRequestException Obnovovací kód nemá správný formát
     */
    public function testResetWithWrongFormatReminder() {
        $this->checkAction('ZapomenuteHeslo:reset', ['reminder' => 'abcWrong']);
    }

    /**
     * @throws \Nette\Application\BadRequestException Obnovovací kód neexistuje
     */
    public function testResetWithWrongUnknownReminder() {
        $this->checkAction('ZapomenuteHeslo:reset', ['reminder' => 'a783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4366']);
    }

    /**
     * @throws \Nette\Application\BadRequestException Obnovovací kód byl již využit
     */
    public function testResetWithAlreadyUsedOne() {
        $this->checkAction('ZapomenuteHeslo:reset', ['reminder' => 'b783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4366']);
    }

    public function testRedirectingToResetForm() {
        $this->checkAction(
            'ZapomenuteHeslo:reset',
            ['reminder' => 'e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80e783eea09f0bc4490175d50b97215f2e6eea9b6dedd408ab80:630bbe50c35d1a99d71a500dde94f16030ef4363']
        );
    }
}


(new ZapomenuteHesloPageTest($container))->run();