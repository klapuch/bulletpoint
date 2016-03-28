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

final class UploadedFile extends TestCase\Filesystem {
	private $uploadedFile;
	const FOLDER = __DIR__ . '/temp/';

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->uploadedFile = new Filesystem\UploadedFile(
			[
				'name' => str_repeat('d1rt.y/../Er~\ ', 20),
				'size' => 20,
				'type' => 'useless',
				'error' => 0,
				'tmp_name' => self::FOLDER . 'file.txt'
			]
		);
	}

	/**
	* @throws \InvalidArgumentException File does not include name key
	*/
	public function testMissingKey() {
		new Filesystem\UploadedFile(
			[
				'tmp_name' => self::FOLDER . 'file.txt'
			]
		);
	}

	public function testName() {
		Assert::same(strlen($this->uploadedFile->name()), 225);
		Assert::same(
			str_repeat('d1rt_y____Er___', 15), $this->uploadedFile->name()
		);
	}

	public function testType() {
		Assert::same($this->uploadedFile->type(), 'text/plain');
	}

	/**
	* @throws \Bulletpoint\Exception\UploadException Soubor nebyl vybrán
	*/
	public function testNotOkFileWithType() {
		(new Filesystem\UploadedFile(
			[
				'name' => 'name',
				'size' => 20,
				'type' => 'useless',
				'error' => UPLOAD_ERR_NO_FILE,
				'tmp_name' => self::FOLDER . 'file.txt'
			]
		))->type();
	}

	public function testSize() {
		Assert::same($this->uploadedFile->size(), 20);
	}

	/**
	* @throws \Bulletpoint\Exception\UploadException Soubor nebyl vybrán
	*/
	public function testNoFileError() {
		(new Filesystem\UploadedFile(
			[
				'name' => 'name',
				'size' => 20,
				'type' => 'useless',
				'error' => UPLOAD_ERR_NO_FILE,
				'tmp_name' => self::FOLDER . 'file.txt'
			]
		))->content();
	}

	public function testLocation() {
		Assert::same($this->uploadedFile->location(), self::FOLDER . 'file.txt');
	}

	public function testError() {
		Assert::same($this->uploadedFile->error(), 0);
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		file_put_contents(self::FOLDER . 'file.txt', 'data');
	}
}


(new UploadedFile())->run();
