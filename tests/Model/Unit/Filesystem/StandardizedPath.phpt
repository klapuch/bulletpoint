<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class StandardizedPath extends \Tester\TestCase {
	private $path;

	protected function setUp() {
		$this->path = new Filesystem\StandardizedPath(
			'C:/Users/Facedown/image.png'
		);
	}

	public function testFolder() {
		Assert::same($this->path->folder(), 'C:/Users/Facedown/');
	}

	public function testFile() {
		Assert::same($this->path->file(), 'image');
	}

	public function testExtension() {
		Assert::same($this->path->extension(), '.png');
		Assert::same((new Filesystem\StandardizedPath('', '', ''))->extension(), '');
	}

	public function testWhole() {
		Assert::same('C:/Users/Facedown/image.png', $this->path->full());
	}
}


(new StandardizedPath())->run();
