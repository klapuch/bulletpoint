<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Report;

final class Complaints implements Report\Complaints {
	public function iterate(): \Iterator {

	}

	public function settle(Report\Target $target) {

	}

	public function complain(Report\Target $target, string $reason) {
		echo $reason;
	}
}