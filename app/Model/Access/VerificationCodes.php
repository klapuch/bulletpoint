<?php
namespace Bulletpoint\Model\Access;

interface VerificationCodes {
    public function generate(string $email): VerificationCode;
}