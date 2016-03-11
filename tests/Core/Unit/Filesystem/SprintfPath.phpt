<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Core\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class SprintfPath extends \Tester\TestCase {
	public function testSprintfFormat() {
		$path = new Filesystem\SprintfPath(
			new Fake\Path('', '', '')
		);
		Assert::same($path->folder(), '%s');
		Assert::same($path->file(), '%s');
		Assert::same($path->extension(), '%s');
	}

	public function testNormalFormat() {
		$path = new Filesystem\SprintfPath(
			new Fake\Path('a', 'b', 'c')
		);
		Assert::same($path->folder(), 'a');
		Assert::same($path->file(), 'b');
		Assert::same($path->extension(), 'c');
	}

	public function testWholeSprintf() {
		$path = new Filesystem\SprintfPath(
			new Fake\Path('', '', '')
		);
		Assert::same('%s%s%s', $path->full());
	}

	public function testWholeNormal() {
		$path = new Filesystem\SprintfPath(
			new Fake\Path('a', 'b', 'c')
		);
		Assert::same('abc', $path->full());
	}
}


(new SprintfPath())->run();
