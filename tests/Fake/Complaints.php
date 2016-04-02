<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Report;

final class Complaints implements Report\Complaints {
	public function iterate(Report\Target $target = null): \Iterator {

	}

	public function settle(Report\Target $target) {

	}

	public function complain(Report\Target $target, string $reason): Report\Complaint {
		return new Complaint($reason);
	}
}