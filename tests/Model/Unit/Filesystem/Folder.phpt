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

final class Folder extends TestCase\Filesystem {
	private $path;

	/**
	 * @var Filesystem\Folder
	 */
	private $folder;
	
	protected function setUp() {
		parent::setUp();
		$this->path = $this->preparedFilesystem();
		$this->folder = new Filesystem\Folder($this->path);
	}

	public function testSaving() {
		$filename = 'textFile.txt';
		$this->folder->save(new Fake\File(null, null, null, 'fooData', null), $filename);
		Assert::same('fooData', file_get_contents($this->path . '/' . $filename));
	}

	public function testUnknownFolder() {
		$subFolder = $this->path . 'subtemp';
		$filename = 'textFile.txt';
		Assert::false(file_exists($subFolder . $filename));
		$folder = new Filesystem\Folder($subFolder);
		$folder->save(new Fake\File(null, null, null, 'fooData', null), $filename);
		Assert::same(file_get_contents($subFolder . '/' . $filename), 'fooData');
	}

	public function testOverwriting() {
		$filename = 'textFile.txt';
		Assert::false(is_file($this->path . $filename));
		file_put_contents($this->path . $filename, '1');
		Assert::true(is_file($this->path . $filename));
		$this->folder->save(new Fake\File(null, null, null, '2', null), $filename);
		Assert::same(file_get_contents($this->path . '/' . $filename), '2');
	}

	public function testSuccessfulLoading() {
		$txtFile = 'file.txt';
		file_put_contents($this->path . $txtFile, 'data');
		Assert::true(is_file($this->path . $txtFile));
		Assert::type(
			'Bulletpoint\Model\Filesystem\SavedFile',
			$this->folder->load($txtFile)
		);
	}

	public function testLoadingOfUnknownFile() {
		Assert::type(
			'Bulletpoint\Model\Filesystem\SavedFile',
			$this->folder->load('loadSomeFileWhichDoeNotExist.foo')
		);
	}

	private function preparedFilesystem() {
		return Tester\FileMock::create('');
	}
}


(new Folder())->run();
