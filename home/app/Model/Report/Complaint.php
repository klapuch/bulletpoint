<?php
namespace Bulletpoint\Model\Report;

use Bulletpoint\Model\Access;

interface Complaint {
	public function id(): int;
	public function critic(): Access\Identity;
	public function target(): Target;
	public function reason(): string;
	public function settle();
}