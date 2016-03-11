<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Email;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class MailMessage extends \Tester\TestCase {
	public function testSubject() {
		Assert::same(
			'=?UTF-8?B?SGkgRmFjZSE=?=',
			(new Email\MailMessage(new Fake\Message('', 'Hi Face!')))->subject()
		);
	}

	public function testReceiver() {
		Assert::same(
			'recipient',
			(new Email\MailMessage(new Fake\Message('recipient')))->recipient()
		);
	}

	public function testSender() {
		Assert::same(
			'sender',
			(new Email\MailMessage(new Fake\Message('', '', '', 'sender')))->sender()
		);
	}

	public function testContent() {
		$content = 'This tests wordwraping function for 70 characters with CRLF breaking character. :)';
		$expected = "This tests wordwraping function for 70 characters with CRLF breaking\r\ncharacter. :)";
		Assert::same(
			$expected,
			(new Email\MailMessage(new Fake\Message('', '', $content)))->content()
		);
	}
}


(new MailMessage())->run();
