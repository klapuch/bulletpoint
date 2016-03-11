<?php
namespace Bulletpoint\Model\Access;

interface VerificationCode {
	public function use();
	public function owner(): Identity;
}