<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\UI;

require __DIR__ . '/../../../bootstrap.php';

final class FlashMessage extends \Tester\TestCase {
	private $flashMessage;

	protected function setUp() {
		$session = [];
		$this->flashMessage = new UI\FlashMessage($session);
	}

	public function testSingleMessage() {
		$this->flashMessage->flash('Message', 'error');
		Assert::same($this->flashMessage->read(), [['error' => 'Message']]);
	}

	public function testEmptyMessage() {
		Assert::same($this->flashMessage->read(), []);
	}

	public function testMultipleMessage() {
		$this->flashMessage->flash('errorMessage', 'error');
		$this->flashMessage->flash('errorMessage', 'error');
		$this->flashMessage->flash('successMessage', 'success');
		Assert::same(
			$this->flashMessage->read(),
			[
				['error' => 'errorMessage'],
				['error' => 'errorMessage'],
				['success' => 'successMessage']
			]
		);
	}

	public function testOnceReadMessage() {
		$this->flashMessage->flash('Message', 'error');
		Assert::same($this->flashMessage->read(), [['error' => 'Message']]);
		Assert::same($this->flashMessage->read(), []);
	}
}


(new FlashMessage())->run();
