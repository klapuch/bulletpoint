<?php
namespace Bulletpoint\TestCase;

abstract class Mockery extends \Tester\TestCase {
	protected function mockery($class) {
		return \Mockery::mock($class);
	}

	protected function tearDown() {
		parent::tearDown();
		\Mockery::close();
	}
}