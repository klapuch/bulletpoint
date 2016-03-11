<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Core\Http;

require __DIR__ . '/../../../bootstrap.php';

final class RoutedParameter extends \Tester\TestCase {
	private $routedParameter;

	protected function setUp() {
		$this->routedParameter = new Http\RoutedParameter(
			new Fake\Address(
				['page', 'view', 'pa','ra','me','ter', '', '  ', 'ter']
			)
		);
	}

	public function testForm() {
		Assert::same(
			$this->routedParameter->parameters(),
			['pa', 'ra', 'me', 'ter', 'ter']
		);
	}
}


(new RoutedParameter())->run();
