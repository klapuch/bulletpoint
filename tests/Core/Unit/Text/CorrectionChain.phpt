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

final class CorrectionChain extends \Tester\TestCase {
	private $correction;

	protected function setUp() {
		$this->correction = new Text\CorrectionChain(
			new Fake\Correction('strtolower'),
			new Fake\Correction('ucfirst')
		);
	}

	/**
	* Firstly is executed strtolower - facedown
	* Then is executed ucfirst - Facedown
	*/
	public function testOrderChain() {
		Assert::same($this->correction->replacement('FACEDOWN'), 'Facedown');
	}
}


(new CorrectionChain())->run();
