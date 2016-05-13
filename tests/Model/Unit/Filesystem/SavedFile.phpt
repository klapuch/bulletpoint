<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Model\Filesystem;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class SavedFile extends TestCase\Filesystem {
	private $path;

	/**
	 * @var Filesystem\SavedFile
	 */
	private $savedFile;

	protected function setUp() {
		parent::setUp();
		$this->path = $this->preparedFilesystem();
		$this->savedFile = new Filesystem\SavedFile(
			new Fake\Path(
				$this->path,
				'file',
				'.txt'
			)
		);
	}

	public function testName() {
		Assert::same($this->savedFile->name(), '1.file.txt');
	}

	public function testType() {
		Assert::same($this->savedFile->type(), 'text/plain');
	}

	public function testSize() {
		Assert::true($this->savedFile->size() > 0);
	}

	public function testContent() {
		Assert::same($this->savedFile->content(), 'data');
	}

	public function testLocation() {
		Assert::same($this->savedFile->location(), $this->path . 'file.txt');
	}

	private function preparedFilesystem() {
		$name = Tester\FileMock::create('');
		file_put_contents($name . 'file.txt', 'data');
		return $name;
	}
}


(new SavedFile())->run();
