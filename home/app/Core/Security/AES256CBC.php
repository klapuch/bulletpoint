<?php
namespace Bulletpoint\Core\Security;

use Bulletpoint\Exception;

final class AES256CBC extends AES implements Cipher {
    const BEGIN = 0;
    const MAC_LENGTH = 64;
    const CIPHER = 'AES-256-CBC';

    public function encrypt(string $password): string {
        $iv = $this->iv();
        $cipherText = openssl_encrypt(
            $this->hashedPassword($password),
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv
        );
        return bin2hex($iv . $cipherText);
    }

    public function decrypt(string $password, string $hashedPassword): bool {
        $binary = hex2bin($hashedPassword);
        $ivSize = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($binary, self::BEGIN, $ivSize);
        $cipherText = substr(
            substr($binary, $ivSize),
            self::BEGIN,
            self::MAC_LENGTH
        );
        $decryptedHash = openssl_decrypt(
            $cipherText,
            self::CIPHER,
            $this->key(),
            OPENSSL_RAW_DATA,
            $iv
        );
        return password_verify($password, $decryptedHash);
    }

    private function iv(): string {
        return random_bytes(openssl_cipher_iv_length(self::CIPHER));
    }

    private function hashedPassword(string $password): string {
        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        if($hash === false)
            throw new \RuntimeException('Error in creating password');
        return $hash;
    }
}