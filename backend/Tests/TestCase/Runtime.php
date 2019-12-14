<?php
declare(strict_types = 1);

namespace Bulletpoint\TestCase;

use Tester;

abstract class Runtime extends Tester\TestCase {
	public function run(): void {
		if (defined('__PHPSTAN_RUNNING__')) {
			Tester\Environment::$checkAssertions = false;
		} else {
			parent::run();
		}
	}
}
