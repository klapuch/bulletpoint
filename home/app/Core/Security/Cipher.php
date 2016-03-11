<?php
namespace Bulletpoint\Core\Security;

interface Cipher {
    public function encrypt(string $plainText): string;
    public function decrypt(string $plainText, string $cipherText): bool;
}