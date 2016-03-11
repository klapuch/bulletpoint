<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Security;

final class Cipher implements Security\Cipher {
	private $onDecrypt;

	public function __construct(bool $onDecrypt = true) {
		$this->onDecrypt = $onDecrypt;
	}

	public function encrypt(string $password): string {
		return 'encrypted';
	}

	public function decrypt(string $plain, string $encrypted): bool {
		return $this->onDecrypt;
	}
}