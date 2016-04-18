<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Model\Security;

final class Cipher implements Security\Cipher {
	private $onDecrypt;
	private $deprecated;

	public function __construct(bool $onDecrypt = true, bool $deprecated = false) {
		$this->onDecrypt = $onDecrypt;
		$this->deprecated = $deprecated;
	}

	public function encrypt(string $password): string {
		return 'encrypted';
	}

	public function decrypt(string $plain, string $encrypted): bool {
		return $this->onDecrypt;
	}

    public function deprecated(string $hash): bool {
        return $this->deprecated;
    }
}