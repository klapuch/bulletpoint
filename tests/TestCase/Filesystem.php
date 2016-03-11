<?php
namespace Bulletpoint\TestCase;

abstract class Filesystem extends Mockery {
	protected function setUp() {
		parent::setUp();
		\Tester\Environment::lock('file', __DIR__ . '/../temp');
	}
}