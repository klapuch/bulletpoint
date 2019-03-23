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
	use TestCase\TemplateDatabase;

	public function testMarchingFilenameWithFilesystem(): void {
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/avatar.png';
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
		(new Domain\Image\UploadedAvatars(
			new Access\FakeUser((string) 1),
			$this->connection,
		))->save();
	}
}

(new UploadedAvatarsTest())->run();
