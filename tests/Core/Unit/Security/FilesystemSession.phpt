<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Core\Security;

require __DIR__ . '/../../../bootstrap.php';

final class FilesystemSession extends TestCase\Filesystem {
	private $session;
	const KEY = 123456789;
	const FOLDER = __DIR__ . '/temp/';

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->session = new Security\FilesystemSession(self::KEY);
		$this->session->open(self::FOLDER, null);
	}

	public function testReading() {
		file_put_contents(self::FOLDER . 'sess_' . md5('valid' . self::KEY), 'data');
		Assert::same($this->session->read('valid'), 'data');
		Assert::same($this->session->read('foooooo'), '');
	}

	public function testWriting() {
		Assert::false(is_file(self::FOLDER . 'sess_' . md5('foo' . self::KEY)));
		$this->session->write('foo', 'data');
		Assert::true(is_file(self::FOLDER . 'sess_' . md5('foo' . self::KEY)));
	}

	public function testDestroying() {
		file_put_contents(self::FOLDER . 'sess_' . md5('foo' . self::KEY), 'data');
		Assert::true(is_file(self::FOLDER . 'sess_' . md5('foo' . self::KEY)));
		Assert::true($this->session->destroy('foo'));
		Assert::false(is_file(self::FOLDER . 'sess_' . md5('foo' . self::KEY)));
		Assert::false($this->session->destroy('xxxxxxxxxxxxxxxxxxxxx'));
	}

	public function testClosing() {
		Assert::true($this->session->close());
	}

	public function testGarbageCollection() {
		file_put_contents(self::FOLDER . 'sess_a', 'data');
		file_put_contents(self::FOLDER . 'sess_b', 'data');
		$this->session->gc($past = -20);
		Assert::false(is_file(self::FOLDER . 'sess_a'));
		Assert::false(is_file(self::FOLDER . 'sess_b'));
		file_put_contents(self::FOLDER . 'sess_a', 'data');
		file_put_contents(self::FOLDER . 'sess_b', 'data');
		$this->session->gc($future = 999);
		Assert::true(is_file(self::FOLDER . 'sess_a'));
		Assert::true(is_file(self::FOLDER . 'sess_b'));
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
	}
}


(new FilesystemSession())->run();
