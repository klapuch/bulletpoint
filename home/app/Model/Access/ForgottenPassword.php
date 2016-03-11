<?php
namespace Bulletpoint\Model\Access;

interface ForgottenPassword {
	public function change(string $password);
}