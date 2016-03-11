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
use Bulletpoint\Core\Control;

require __DIR__ . '/../../../bootstrap.php';

final class FilesystemLog extends TestCase\Filesystem {
	private $log;
	const FOLDER = __DIR__ . '/temp/';

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->log = new Control\FilesystemLog(
			new Fake\Path(self::FOLDER, 'error', '.txt')
		);
	}

	public function testErrorWriting() {
		Assert::same(file_get_contents(self::FOLDER . 'error.txt'), '');
		$this->log->write(['TYPE: 3']);
		Assert::notSame(file_get_contents(self::FOLDER . 'error.txt'), '');
	}

	public function testErrorAppendedWriting() {
		Assert::same(strlen(file_get_contents(self::FOLDER . 'error.txt')), 0);
		$this->log->write(['TYPE: 3']);
		$firstLog = strlen(file_get_contents(self::FOLDER . 'error.txt'));
		$this->log->write(['TYPE: 3']);
		$secondLog = strlen(file_get_contents(self::FOLDER . 'error.txt'));
		Assert::true($secondLog > $firstLog);
	}

	public function testWithoutError() {
		$this->log->write([]);
		Assert::same(file_get_contents(self::FOLDER . 'error.txt'), '');
	}

	public function testReading() {
		file_put_contents(self::FOLDER . 'error.txt', 'someData');
		Assert::same($this->log->read(), 'someData');
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		touch(self::FOLDER . 'error.txt');
	}
}


(new FilesystemLog())->run();
