<?php
namespace Bulletpoint\Model\Report;

interface Complaints {
	public function iterate(): \Iterator;
	public function complain(Target $target, string $reason);
	public function settle(Target $target);
}