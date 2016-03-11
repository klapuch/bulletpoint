<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;
use Bulletpoint\Fake;

require __DIR__ . '/../../../bootstrap.php';

final class AdaptableCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		$this->correction = new Text\AdaptableCorrection(new Fake\Correction);
	}

	protected function validInputs() {
		return [
			[['a', ['a', ['a']], 'b', ['b']]],
			[['a', 'b', 'c']],
			['facedown'],
			[['good' => 'ok', ['bad' => 'ko']]]
		];
	}

	/**
	* @dataProvider validInputs
	*/
	public function testValidInputs($actual) {
		Assert::same($this->correction->replacement($actual), $actual);
	}
}


(new AdaptableCorrection())->run();
