<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Model\Access;

interface Profile {
	public function comments(): int;
	public function bulletpoints(): int;
	public function documents(): int;
	public function owner(): Access\Identity;
}