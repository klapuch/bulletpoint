<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Image;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Storage;
use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class UploadedAvatarsTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase {
		tearDown as databaseTeardown;
	}

	public function testMatchingDatabaseFilenameWithFilesystemName(): void {
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/avatar.png';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'avatar.png';
		(new Domain\Image\UploadedAvatars(
			new Access\FakeUser((string) $user),
			$this->connection,
		))->save();
		$filename = (new Storage\TypedQuery($this->connection, 'SELECT avatar_filename FROM users'))->field();
		Assert::true(file_exists(sprintf(__DIR__ . '/../../../../data/%s', $filename)));
		@unlink(sprintf(__DIR__ . '/../../../../data/%s', $filename));
	}

	/**
	 * @throws \UnexpectedValueException File is not an image
	 */
	public function testThrowingOnNotImage(): void {
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/text.txt';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'text.txt';
		(new Domain\Image\UploadedAvatars(
			new Access\FakeUser((string) 1),
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
		(new Domain\Image\UploadedAvatars(
			new Access\FakeUser((string) 1),
			$this->connection,
		))->save();
	}

	protected function tearDown(): void {
		$this->databaseTeardown();
		copy(__DIR__ . '/fixtures/avatar.sample.png', __DIR__ . '/fixtures/avatar.png');
	}
}

(new UploadedAvatarsTest())->run();
