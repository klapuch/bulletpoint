<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Model\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\Model\User;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Core\Filesystem;

require __DIR__ . '/../../../bootstrap.php';

final class ProfilePhoto extends TestCase\Filesystem {
	const FOLDER = __DIR__ . '/temp';
	const IDENTITY_IMAGE = self::FOLDER . '/1.png';
	const DEFAULT_IMAGE = self::FOLDER . '/default/profile.png';

	public function setUp() {
		parent::setUp();
		$this->preparedFilesystem();
	}

	public function testShowing() {
		$file = (new User\ProfilePhoto(
			new Fake\Identity(1),
			new Filesystem\Folder(self::FOLDER)
		))->show()->asFile();
		Assert::same($file->location(), self::IDENTITY_IMAGE);
	}

	public function testShowingDefaultPhoto() {
		$file = (new User\ProfilePhoto(
			new Fake\Identity(2),
			new Filesystem\Folder(self::FOLDER)
		))->show()->asFile();
		Assert::same($file->location(), self::DEFAULT_IMAGE);
	}

	public function testChanging() {
		(new User\ProfilePhoto(
			new Fake\Identity(2),
			new Filesystem\Folder(self::FOLDER)
		))->change(
			new Filesystem\Image(
				new Fake\File(
					'1.png',
					'image/png',
					100,
					'',
					self::IDENTITY_IMAGE
				)
			)
		);
		list($width, $height) = getimagesize(self::FOLDER . '/2.png');
		Assert::same(64, $width);
		Assert::same(64, $height);
	}

	private function preparedFilesystem() {
		Tester\Helpers::purge(self::FOLDER);
		Tester\Helpers::purge(self::FOLDER . '/default');
		$this->prepareImages();
	}

    private function prepareImages() {
		$identityImage = imagecreatetruecolor(128, 256);
		$defaultImage = imagecreatetruecolor(128, 256);
		imagepng($identityImage, self::IDENTITY_IMAGE);
		imagepng($defaultImage, self::DEFAULT_IMAGE);
		imagedestroy($identityImage);
		imagedestroy($defaultImage);
    }
}


(new ProfilePhoto())->run();
