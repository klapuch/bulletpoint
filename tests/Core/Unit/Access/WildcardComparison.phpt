<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Access;

require __DIR__ . '/../../../bootstrap.php';

final class WildcardComparison extends \Tester\TestCase {
	private $comparison;

	protected function setUp() {
		$this->comparison = new Access\WildcardComparison();
	}

	protected function validMatches() {
		return [
			['*abc', '123654wwwABC'],
			['abc*', 'abc45623foo'],
			['*abc*', '~123xxxabcyyy456~'],
			['*abc', 'abcdefabc'],
			['abc*', 'abcabcabcabc'],
			['*abc*', '123abc123'],
			['*abc*', '4abc4'],
			['*', '12abcdef123'],
			['', ''],
			['?abc', 'aabc'],
			['abc?', 'abcd'],
			['?abc?', 'xabcy'],
			['???', 'abc'],
			['?', 'a'],
			['a?bc*d', 'aXbcFood'],
			['abc', 'abc'],
			['abc*', 'abc'],
			['abc?', 'abc'],
			['*', ''],
			['?', ''],
		];
	}

	/**
	* @dataProvider validMatches
	*/
	public function testValidMatches($withWildcard, $expected) {
		Assert::true($this->comparison->areSame($withWildcard, $expected));
	}

	protected function invalidMatches() {
		return [
			['?', 'abcdd'],
			['abc', 'abcd'],
			['a?', 'abcd'],
			['?a?', 'www'],
		];
	}

	/**
	* @dataProvider invalidMatches
	*/
	public function testInvalidMatches($withWildcard, $expected) {
		Assert::false($this->comparison->areSame($withWildcard, $expected));
	}
}


(new WildcardComparison())->run();
