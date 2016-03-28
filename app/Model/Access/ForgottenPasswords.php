<?php
namespace Bulletpoint\Model\Access;

interface ForgottenPasswords {
    public function remind(string $email);
}