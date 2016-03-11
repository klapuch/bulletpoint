<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class FillRule extends \Tester\TestCase {
	protected function emptyStrings() {
		return [
			[false],
			['    '],
			[''],
		];
	}

	/**
	* @dataProvider emptyStrings
	*/
	public function testEmptyStrings($string) {
		Assert::exception(function() use ($string) {
			(new Constraint\FillRule('Msg'))->isSatisfied($string);
		}, 'Bulletpoint\Exception\FormatException', 'Msg');
	}

	public function testValidString() {
		(new Constraint\FillRule('Nothing was thrown'))
		->isSatisfied('ab  c  ');
		Assert::true(true);
	}
}


(new FillRule())->run();
