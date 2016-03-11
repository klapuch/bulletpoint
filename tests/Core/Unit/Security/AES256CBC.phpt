<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester\Assert;
use Bulletpoint\Core\Security;

require __DIR__ . '/../../../bootstrap.php';

final class AES256CBC extends \Tester\TestCase {
	private $cipher;
	const LENGTH = 160;
	const KEY = '\x1d\x6b\x3e\x91\x66\xdf\xb9\x90\x80\xf5\x03\xac\x6a\x3b\xcd\xae';
	const ENCRYPTED_PASSWORD = '7538976fdc56243c9e8cf3f73c921cfabb5cf0caaae7a7841b04931bc4e31d92ce496da7821e01ebc1702ac2fbd786c376fa8cf4d171d4ba0047797360759890ac6dbb9b17aafdcfd7e646ac083a0c03';
	const DECRYPTED_PASSWORD = '123456';

	protected function setUp() {
		$this->cipher = new Security\AES256CBC(self::KEY);
	}

	public function testCorrectPassword() {
		Assert::true($this->cipher->decrypt(self::DECRYPTED_PASSWORD, self::ENCRYPTED_PASSWORD));
	}

 	/**
 	* @dataProvider samplePasswords
 	*/
	public function testValidFormats($password) {
		Assert::same(self::LENGTH, mb_strlen($this->cipher->encrypt($password), 'UTF-8'));
	}

	/**
	* @dataProvider samplePasswords
	*/
	public function testCorrectPasswords($password) {
		Assert::true($this->cipher->decrypt($password, $this->cipher->encrypt($password)));
	}

	protected function samplePasswords() {
		return [
			[''],
			['foo'],
			[hash('SHA512', time())]
		];
	}
}


(new AES256CBC())->run();
