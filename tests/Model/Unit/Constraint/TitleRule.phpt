<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class TitleRule extends \Tester\TestCase {
	protected function validTitles() {
		return [
			['abc'],
			['ABC'],
			['ahoj5'],
			['5ahoj'],
			['555a'],
			['how are you?'],
		];
	}

	protected function invalidTitles() {
		return [
			['123456'],
			[123456],
			[0],
			['0'],
			['0000000'],
		];
	}

	/**
	* @dataProvider validTitles
	*/
	public function testValidTitles($title) {
		(new Constraint\TitleRule)->isSatisfied($title);
		Assert::true(true);
	}

	/**
	* @dataProvider invalidTitles
	*/
	public function testInvalidTitles($title) {
		Assert::exception(function() use($title) {
			(new Constraint\TitleRule)->isSatisfied($title);
		}, 'Bulletpoint\Exception\FormatException', 'Titulek musí být alfabetický');
	}
}


(new TitleRule())->run();
