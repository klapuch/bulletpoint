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

final class AutoLoader extends TestCase\Filesystem {
	private $autoLoader;
	const TEMP_LOCATION = __DIR__ . '/temp';
	const ROOT_LOCATION = __DIR__ . '/';

	protected function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
		$this->autoLoader = new Control\AutoLoader(
			[self::TEMP_LOCATION, self::ROOT_LOCATION]
		);
	}

	protected function foundClasses() {
		return [
			['Bulletpoint\Core\MyClass'],
			['Bulletpoint\Core\Foo\MyClass2'],
		];
	}

	/**
	* @dataProvider foundClasses
	*/
	public function testFoundClasses($class) {
		Assert::truthy($this->autoLoader->load($class));
	}

	/**
	* @dataProvider foundClasses
	*/
	public function testFoundClassWithFlipOrder($class) {
		$autoLoader = new Control\AutoLoader(
			[self::ROOT_LOCATION, self::TEMP_LOCATION]
		);
		Assert::truthy($autoLoader->load($class));
	}

	public function testNotFoundClass() {
		Assert::exception(function() {
			$this->autoLoader->load('Bulletpoint\Core\FooRandomClass');
		}, 'UnexpectedValueException',
		sprintf(
			'Class %s does not exist in %s folder(s)',
			'Bulletpoint\Core\FooRandomClass',
			self::TEMP_LOCATION . ' or ' . self::ROOT_LOCATION
		));
	}

	protected function unsuitableNamespaces() {
		return [
			['Class'],
			['\Class'],
			['Class\\'],
		];
	}

	/**
	* @dataProvider unsuitableNamespaces
	*/
	public function testUnsuitableNamespace($class) {
		Assert::exception(function() use ($class) {
			$this->autoLoader->load($class);
		}, 'UnexpectedValueException',
		sprintf(
			'Namespace in %s class does not have suitable name',
			$class
		));
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::TEMP_LOCATION);
		Tester\Helpers::purge(self::TEMP_LOCATION . '/Core');
		Tester\Helpers::purge(self::TEMP_LOCATION . '/Core/Foo');
		touch(self::TEMP_LOCATION . '/Core/MyClass.php');
		touch(self::TEMP_LOCATION . '/Core/Foo/MyClass2.php');
	}
}


(new AutoLoader())->run();
