<?php
namespace Bulletpoint\Model\Report;

interface Target {
	public function id(): int;
	public function complaints(): \Iterator;
}