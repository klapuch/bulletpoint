<?php
namespace Bulletpoint\Model\Security;

interface Cipher {
    public function encrypt(string $plainText): string;
    public function decrypt(string $plainText, string $cipherText): bool;
}