<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Core\Control;

require __DIR__ . '/../../../bootstrap.php';

final class IniConfiguration extends TestCase\Filesystem {
	private $ini;

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->ini = new Control\IniConfiguration(__DIR__ . '/temp/config.ini');
	}

	public function testSections() {
		Assert::same($this->ini->toSection('parents')->dad, 1);
		Assert::same($this->ini->toSection('DESCENDANT')->bro, '3');
	}

	public function testSettingWithSection() {
		Assert::same(
			$this->ini->toSection('parents')->setting(),
			['dad' => 1, 'mom' => 2]
		);
	}

	public function testSettingWithoutSection() {
		Assert::same(
			$this->ini->setting(),
			[
				'PARENTS' => ['dad' => 1, 'mom' => 2],
				'DESCENDANT' => ['bro' => '3', 'sis' => '4'],
				'PROPERTIES' => ['good' => true, 'bad' => false]
			]
		);
	}

	/**
	* @throws RuntimeException bar is undefined setting
	*/
	public function testUndefinedSettingWithSection() {
		$this->ini->toSection('parents')->bar;
	}

	/**
	* @throws RuntimeException bar is undefined setting
	*/
	public function testUndefinedSettingWithoutSection() {
		$this->ini->bar;
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(__DIR__ . '/temp/');
		file_put_contents(__DIR__ . '/temp/config.ini', PATTERN);
	}
}

const PATTERN = <<<INI
[PARENTS]
dad = 1
mom = 2
[DESCENDANT]
bro = "3"
sis = "4"
[PROPERTIES]
good = true
bad = false
INI;


(new IniConfiguration())->run();
