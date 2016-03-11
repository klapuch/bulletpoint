<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Core\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class ExistingPath extends TestCase\Filesystem {
	const FOLDER = __DIR__ . '/temp/';
	
	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
	}

	public function testExistingFolder() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(self::FOLDER, '', '')
		);
		Assert::same($path->folder(), self::FOLDER);
	}

	/**
	* @throws RuntimeException fooFolderBar is not a folder
	*/
	public function testUnknownFolder() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path('fooFolderBar', '', '')
		);
		Assert::same($path->folder(), 'fooFolderBar');
	}

	public function testExistingFile() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(self::FOLDER, 'layout', '.phtml')
		);
		Assert::same($path->file(), 'layout');
	}

	/**
	* @throws RuntimeException fooLayout.phtml does not exist
	*/
	public function testUnknownFile() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(self::FOLDER, 'fooLayout', '.phtml')
		);
		Assert::same($path->file(), 'fooLayout');
	}

	public function testExtension() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path('', '', '.phtml')
		);
		Assert::same($path->extension(), '.phtml');
	}

	public function testWhole() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(self::FOLDER, 'layout', '.phtml')
		);
		Assert::same($path->full(), self::FOLDER . 'layout.phtml');
	}

	/**
	* @throws RuntimeException fooFile.foo does not exist
	*/
	public function testUnknownWhole() {
		$path = new Filesystem\ExistingPath(
			new Fake\Path(self::FOLDER, 'fooFile', '.foo')
		);
		$path->full();
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . 'layout.phtml', 'data');
	}
}


(new ExistingPath())->run();
