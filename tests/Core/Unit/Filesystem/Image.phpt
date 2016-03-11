<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Core\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class Image extends TestCase\Filesystem {
	const FOLDER = __DIR__ . '/temp';
	const SOURCE_IMAGE = self::FOLDER . '/image.png';
	private $image;
	
	protected function setUp() {
		parent::setUp();
		$this->image = new Filesystem\Image(
			new Fake\File(
				'image.png',
				'image/png',
				666,
				'content',
				self::SOURCE_IMAGE
			)
		);
	}

	public function testResize() {
		$this->preparedFilesystem();
		$size = new Filesystem\Size(64, 64);
		$image = $this->image->resize($size);
		Assert::type('Bulletpoint\Core\Filesystem\Image', $image);
		Assert::same((string)$image->size(), (string)$size);
	}

	public function testNotNeccesaryResize() {
		$this->preparedFilesystem();
		$size = new Filesystem\Size(128, 256);
		$image = $this->image->resize($size);
		Assert::type('Bulletpoint\Core\Filesystem\Image', $image);
		Assert::same((string)$image->size(), (string)$size);
		Assert::notSame($image, $this->image);
	}

	public function testImageAsFile() {
		$imageAsFile = $this->image->asFile();
		Assert::same($imageAsFile->name(), 'image.png');
		Assert::same($imageAsFile->type(), 'image/png');
		Assert::same($imageAsFile->content(), 'content');
		Assert::same($imageAsFile->size(), 666);
		Assert::same($imageAsFile->location(), self::SOURCE_IMAGE);
	}

	public function testAllowedExtension() {
		new Filesystem\Image(new Fake\File(null, 'image/jpeg'));
		new Filesystem\Image(new Fake\File(null, 'image/gif'));
		new Filesystem\Image(new Fake\File(null, 'image/png'));
		Assert::true(true);
	}

	/**
	* @throws Bulletpoint\Exception\StorageException Soubor typu bmp není obrázek. Povolené typy jsou jpeg, png, gif
	*/
	public function testDisallowedExtension() {
		new Filesystem\Image(new Fake\File(null, 'image/bmp'));
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		$this->prepareImage();
	}

    private function prepareImage() {
		$image = imagecreatetruecolor(128, 256);
		imagepng($image, self::SOURCE_IMAGE);
		imagedestroy($image);
    }
}


(new Image())->run();
