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
	const FOLDER = __DIR__ . '/temp/';
	private $folder;
	
	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->folder = new Filesystem\Folder(self::FOLDER);
	}

	public function testCommonSaving() {
		$filename = 'textFile.txt';
		Assert::false(is_file(self::FOLDER . $filename));
		$this->folder->save(new Fake\File(null, null, null, 'fooData', null), $filename);
		Assert::true(is_file(self::FOLDER . $filename));
	}

	public function testSavingWithUknownFolder() {
		$subFolder = self::FOLDER . 'subtemp/';
		$filename = 'textFile.txt';
		Assert::false(is_file($subFolder . $filename));
		Assert::false(is_dir($subFolder));
		$folder = new Filesystem\Folder($subFolder);
		$folder->save(new Fake\File(null, null, null, 'fooData', null), $filename);
		Assert::same(file_get_contents($subFolder . $filename), 'fooData');
	}

	public function testSavingWithOverwriting() {
		$filename = 'textFile.txt';
		Assert::false(is_file(self::FOLDER . $filename));
		file_put_contents(self::FOLDER . $filename, '1');
		Assert::true(is_file(self::FOLDER . $filename));
		$this->folder->save(new Fake\File(null, null, null, '2', null), $filename);
		Assert::same(file_get_contents(self::FOLDER . $filename), '2');
	}

	public function testSuccessfulLoading() {
		$file = $this->folder->load('file.txt');
		Assert::type(
			'Bulletpoint\Model\Filesystem\SavedFile',
			$file
		);
		Assert::same($file->content(), 'data');
	}

	/**
	* @throws \RuntimeException unknownFile.txt does not exist
	*/
	public function testLoadingOfUnknownFile() {
		$unknownFile = 'unknownFile.txt';
		Assert::false(is_file(self::FOLDER . $unknownFile));
		$this->folder->load($unknownFile)->content();
		Assert::false(is_file(self::FOLDER . $unknownFile));
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . 'file.txt', 'data');
	}
}


(new Folder())->run();
