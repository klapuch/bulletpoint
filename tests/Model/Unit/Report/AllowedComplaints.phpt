<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester\Assert;
use Bulletpoint\Model\Report;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Model\Access;

require __DIR__ . '/../../../bootstrap.php';

final class AllowedComplaints extends \Tester\TestCase {
	public function testAllowedReason() {
		$complaints = new Report\AllowedComplaints(new Fake\Complaints);
		ob_start();
		$complaints->complain(new Fake\Target, 'vulgarita');
		Assert::same(ob_get_contents(), 'vulgarita');
		ob_clean();
		$complaints->complain(new Fake\Target, 'VulGarita');
		Assert::same(ob_get_clean(), 'VulGarita');

	}

	public function testDisallowedReason() {
		ob_start();
		(new Report\AllowedComplaints(new Fake\Complaints))
		->complain(new Fake\Target, 'fooooo?');
		Assert::same(ob_get_clean(), 'Jiné');
	}
}


(new AllowedComplaints())->run();
