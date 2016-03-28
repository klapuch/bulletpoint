<?php
namespace Bulletpoint\Model\Report;

interface Complaints {
    public function iterate(Target $target): \Iterator;
    public function complain(Target $target, string $reason): Complaint;
    public function settle(Target $target);
}