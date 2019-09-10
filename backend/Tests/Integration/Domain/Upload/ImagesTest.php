<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Upload;

use Bulletpoint\Domain;
use Bulletpoint\TestCase;
use Klapuch\Storage;
use Nette\Http\FileUpload;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ImagesTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase {
		tearDown as databaseTeardown;
		setUp as databaseSetUp;
	}

	protected function setUp(): void {
		$this->databaseSetUp();
		Environment::lock('images_test', __DIR__ . '/../../../temp');
	}

	public function testMatchingDatabaseFilenameWithFilesystemName(): void {
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/avatar.png';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'avatar.png';
		(new Domain\Upload\Images(
			'images/avatars',
			new FileUpload($_FILES['avatar']),
			$this->connection,
		))->save();
		$filename = (new Storage\TypedQuery($this->connection, 'SELECT filename FROM filesystem.files'))->field();
		Assert::true(file_exists(sprintf(__DIR__ . '/../../../../data/%s', $filename)));
		@unlink(sprintf(__DIR__ . '/../../../../data/%s', $filename));
	}

	public function testCheckingProperties(): void {
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/avatar.png';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'avatar.png';
		(new Domain\Upload\Images(
			'images/avatars',
			new FileUpload($_FILES['avatar']),
			$this->connection,
		))->save();
		$file = (new Storage\TypedQuery($this->connection, 'SELECT * FROM filesystem.files$images'))->row();
		Assert::same('image/png', $file['mime_type']);
		Assert::same(1, $file['id']);
		Assert::same(100, $file['size_bytes']);
		Assert::same(180, $file['width']);
		Assert::same(180, $file['height']);
		Assert::match('images/avatars/000/000/000/001/1-%h%.png', $file['filename']);
		@unlink(sprintf(__DIR__ . '/../../../../data/%s', $file['filename']));
	}

	/**
	 * @throws \UnexpectedValueException File is not an image
	 */
	public function testThrowingOnNotImage(): void {
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/text.txt';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'text.txt';
		(new Domain\Upload\Images(
			'images/avatars',
			new FileUpload($_FILES['avatar']),
			$this->connection,
		))->save();
	}

	/**
	 * @throws \UnexpectedValueException File was not successfully uploaded
	 */
	public function testThrowingOnNotSuccessfulUpload(): void {
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/text.txt';
		$_FILES['avatar']['error'] = UPLOAD_ERR_NO_FILE;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'text.txt';
		(new Domain\Upload\Images(
			'images/avatars',
			new FileUpload($_FILES['avatar']),
			$this->connection,
		))->save();
	}

	protected function tearDown(): void {
		$this->databaseTeardown();
		copy(__DIR__ . '/fixtures/avatar.sample.png', __DIR__ . '/fixtures/avatar.png');
	}
}

(new ImagesTest())->run();
