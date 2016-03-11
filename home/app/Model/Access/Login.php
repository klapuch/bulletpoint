<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\User;

interface Login {
	public function enter(User\User $user): Identity;
}