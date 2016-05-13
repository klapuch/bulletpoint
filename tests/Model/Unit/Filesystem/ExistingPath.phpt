<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class ExistingPath extends TestCase\Filesystem {
	private $path;
	
	protected function setUp() {
		parent::setUp();
		$this->path = $this->preparedFilesystem();
	}

	public function testFolder() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(__DIR__, '', '')
		);
		Assert::same($path->folder(), __DIR__);
	}

	public function testExistingFile() {
		file_put_contents($this->path . 'layout.phtml', '<html>');
		$path = new Filesystem\ExistingPath(
			new Fake\Path($this->path, 'layout', '.phtml')
		);
		Assert::same($path->file(), 'layout');
	}

	/**
	* @throws \RuntimeException fooLayout.phtml does not exist
	*/
	public function testUnknownFile() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path($this->path, 'fooLayout', '.phtml')
		);
		$path->file();
	}

	public function testExtension() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path('', '', '.phtml')
		);
		Assert::same($path->extension(), '.phtml');
	}

	public function testWhole() {
		file_put_contents($this->path . 'layout.phtml', '<html>');
		$path = new Filesystem\ExistingPath(
			new Fake\Path($this->path, 'layout', '.phtml')
		);
		Assert::same($path->full(), $this->path . 'layout.phtml');
	}

	/**
	* @throws \RuntimeException fooFile.foo does not exist
	*/
	public function testUnknownWhole() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path($this->path, 'fooFile', '.foo')
		);
		$path->full();
	}

	private function preparedFilesystem() {
		return Tester\FileMock::create('');
	}
}


(new ExistingPath())->run();
