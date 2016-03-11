<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class StandardizedPath extends \Tester\TestCase {
	private $path;

	protected function setUp() {
		$this->path = new Filesystem\StandardizedPath(
			'/folder/\\', '/file/', '.extension..'
		);
	}

	public function testFullPathConstructor() {
		$path = new Filesystem\StandardizedPath(
			'C:/Users/Facedown/image.png'
		);
		Assert::same($path->folder(), 'C:/Users/Facedown/');
		Assert::same($path->file(), 'image');
		Assert::same($path->extension(), '.png');
	}

	public function testFolder() {
		Assert::same($this->path->folder(), '/folder/');
	}

	public function testFile() {
		Assert::same($this->path->file(), 'file');
	}

	public function testExtension() {
		Assert::same($this->path->extension(), '.extension');
		Assert::same((new Filesystem\StandardizedPath('', '', ''))->extension(), '');
	}

	public function testWhole() {
		Assert::same('/folder/file.extension', $this->path->full());
	}
}


(new StandardizedPath())->run();
