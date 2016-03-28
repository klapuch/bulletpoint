<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\Fake;
use Bulletpoint\Model\Security;

require __DIR__ . '/../../../bootstrap.php';

final class ImageCaptcha extends \Tester\TestCase {
	private $captcha;
	const RANDOM = '1234';

	public function setUp() {
		$this->captcha = new Security\ImageCaptcha(self::RANDOM);
	}

	public function testCorrectAnswer() {
		$this->captcha->verify(self::RANDOM);
		$this->captcha->verify('  ' . self::RANDOM . '   ');
		Assert::true(true);
	}

	/**
	* @throws \Bulletpoint\Exception\AccessDeniedException Opsaný text z obrázku není správný
	*/
	public function testWrongAnswer() {
		$this->captcha->verify('blah');
		$this->captcha->verify(self::RANDOM . '0000');
	}

	/**
	* @throws \Bulletpoint\Exception\AccessDeniedException Opsaný text z obrázku není správný
	*/
	public function testRandomness() {
        (new Security\ImageCaptcha())->verify('1234');
	}

	public function testGenerate() {
		$dom = Tester\DomQuery::fromHtml((string)$this->captcha);
		Assert::true($dom->has('img[width="50"]'));
		Assert::true($dom->has('img[height="30"]'));
		Assert::contains('<img src="data:image/png;base64,', (string)$this->captcha);
	}
}


(new ImageCaptcha())->run();
