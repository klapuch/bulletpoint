<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;

require __DIR__ . '/../../../bootstrap.php';

final class YearRule extends \Tester\TestCase {
	private $year;

	public function setUp() {
		$this->year = new Constraint\YearRule();
	}
	protected function validYears() {
		return [
			[''],
			[null],
			[1],
			[10],
			[100],
			[2000],
			[date('Y')],
			['2000'],
		];
	}

	/**
	* @dataProvider validYears
	*/
	public function testValidYears($year) {
		$this->year->isSatisfied($year);
		Assert::true(true);
	}

	protected function invalidYears() {
		return [
			[0],
			['0'],
			[-10],
			[-100],
			[date('Y') + 1],
			[date('Y') + 10],
			['abcd'],
			['000000000']
		];
	}

	/**
	* @dataProvider invalidYears
	*/
	public function testInvalidYears($year) {
		Assert::exception(function() use ($year) {
			$this->year->isSatisfied($year);
		}, 'Bulletpoint\Exception\FormatException', 'Rok musí být celé číslo menší než rok aktuální');
	}
}


(new YearRule())->run();
