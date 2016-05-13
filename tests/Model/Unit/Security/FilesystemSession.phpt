<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Model\Security;

require __DIR__ . '/../../../bootstrap.php';

final class FilesystemSession extends TestCase\Filesystem {
	const KEY = 123456789;
	private $path;

	/**
	 * @var Security\FilesystemSession
	 */
	private $session;

	protected function setUp() {
		parent::setUp();
		$this->path = $this->preparedFilesystem();
		$this->session = new Security\FilesystemSession(self::KEY);
		$this->session->open($this->path, null);
	}

	public function testReading() {
		file_put_contents($this->path . '/sess_' . md5('valid' . self::KEY), 'data');
		Assert::same($this->session->read('valid'), 'data');
		Assert::same($this->session->read('foooooo'), '');
	}

	public function testWriting() {
		Assert::false(is_file($this->path . '/sess_' . md5('foo' . self::KEY)));
		$this->session->write('foo', 'data');
		Assert::true(is_file($this->path . '/sess_' . md5('foo' . self::KEY)));
	}

	public function testDestroying() {
		file_put_contents($this->path . '/sess_' . md5('foo' . self::KEY), 'data');
		Assert::true(is_file($this->path . '/sess_' . md5('foo' . self::KEY)));
		Assert::true($this->session->destroy('foo'));
		Assert::false($this->session->destroy('xxxxxxxxxxxxxxxxxxxxx'));
	}

	public function testClosing() {
		Assert::true($this->session->close());
	}

	public function testGarbageCollection() {
		file_put_contents($this->path . '/sess_a', 'data');
		file_put_contents($this->path . '/sess_b', 'data');
		$this->session->gc($past = -20);
		Assert::false(is_file($this->path . 'sess_a'));
		Assert::false(is_file($this->path . 'sess_b'));
		file_put_contents($this->path . 'sess_a', 'data');
		file_put_contents($this->path . 'sess_b', 'data');
		$this->session->gc($future = 999);
		Assert::true(is_file($this->path . 'sess_a'));
		Assert::true(is_file($this->path . 'sess_b'));
	}

	private function preparedFilesystem() {
		return Tester\FileMock::create('');
	}
}


(new FilesystemSession())->run();
