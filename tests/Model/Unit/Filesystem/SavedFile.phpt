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
	private $savedFile;
	const FOLDER = __DIR__ . '/temp/';

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->savedFile = new Filesystem\SavedFile(
			new Fake\Path(
				self::FOLDER,
				'file',
				'.txt'
			)
		);
	}

	public function testName() {
		Assert::same($this->savedFile->name(), 'file.txt');
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
		Assert::same($this->savedFile->location(), self::FOLDER . 'file.txt');
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . 'file.txt', 'data');
	}
}


(new SavedFile())->run();
