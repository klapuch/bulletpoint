<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class SuffixCorrection extends \Tester\TestCase {
	private $correction;
	const SUFFIX = 'Down';

	protected function setUp() {
		$this->correction = new Text\SuffixCorrection(self::SUFFIX);
	}

	public function testSuffix() {
		Assert::same(
			'Face' . self::SUFFIX,
			$this->correction->replacement('Face')
		);
	}
}


(new SuffixCorrection())->run();
