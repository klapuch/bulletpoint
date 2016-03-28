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
	public function testCaseInsensitiveAllowedReason() {
		$complaints = new Report\AllowedComplaints(new Fake\Complaints);
		Assert::equal(new Fake\Complaint('vulgarita'), $complaints->complain(new Report\Target(666), 'vulgarita'));
		Assert::equal(new Fake\Complaint('Jiné'), $complaints->complain(new Report\Target(666), 'Jiné'));
		Assert::equal(new Fake\Complaint('VulGarita'), $complaints->complain(new Report\Target(666), 'VulGarita'));
	}

	public function testDisallowedReason() {
		$complaint = (new Report\AllowedComplaints(new Fake\Complaints))
		->complain(new Report\Target(666), 'fooooo?');
		Assert::equal(new Fake\Complaint('Jiné'), $complaint);
	}
}


(new AllowedComplaints())->run();
