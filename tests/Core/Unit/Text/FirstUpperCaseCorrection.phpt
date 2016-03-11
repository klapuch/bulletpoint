<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Text;

require __DIR__ . '/../../../bootstrap.php';

final class FirstUpperCaseCorrection extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		mb_internal_encoding('UTF-8');
		$this->correction = new Text\FirstUpperCaseCorrection();
	}

	public function texts() {
		return [
			['facedown', 'Facedown'],
			['Facedown', 'Facedown'],
			['facedowN', 'FacedowN']
		];
	}

	/**
	* @dataProvider texts
	*/
	public function testFormat($origin, $corrected) {
		Assert::same(
			$this->correction->replacement($origin),
			$corrected
		);
	}
}


(new FirstUpperCaseCorrection())->run();
