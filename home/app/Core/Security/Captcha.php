<?php
namespace Bulletpoint\Core\Security;

interface Captcha {
	public function verify(string $answer);
	public function __toString();
}