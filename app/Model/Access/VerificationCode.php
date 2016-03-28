<?php
namespace Bulletpoint\Model\Access;

interface VerificationCode {
    public function use(): self;
    public function owner(): Identity;
}