<?php
namespace Bulletpoint\Model\Security;

interface Captcha {
    public function verify(string $answer);
    public function __toString();
}