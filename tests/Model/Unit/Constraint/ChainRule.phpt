<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Constraint;
use Bulletpoint\Fake;
use Bulletpoint\TestCase;

require __DIR__ . '/../../../bootstrap.php';

final class ChainRule extends TestCase\Mockery {
	public function testOrder() {
		$rule = $this->mockery('Bulletpoint\Model\Constraint\Rule');
		(new Constraint\ChainRule(
			$rule->shouldReceive('isSatisfied')->once()->mock(),			
			$rule->shouldReceive('isSatisfied')->once()->mock(),			
			$rule->shouldReceive('isSatisfied')->once()->mock(),			
			$rule->shouldReceive('isSatisfied')->once()->mock()	
		))->isSatisfied(true);
		Assert::true(true);
	}
}


(new ChainRule())->run();
