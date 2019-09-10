<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Upload;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Storage;
use Nette\Http\FileUpload;
use Tester\Assert;
use Tester\Environment;

require __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class AvatarsTest extends TestCase\Runtime {
	use TestCase\TemplateDatabase {
		tearDown as databaseTeardown;
		setUp as databaseSetUp;
	}

	protected function setUp(): void {
		$this->databaseSetUp();
		Environment::lock('images_test', __DIR__ . '/../../../temp');
	}

	public function testUploadingToUserProfile(): void {
		['id' => $user] = (new Fixtures\SamplePostgresData($this->connection, 'users'))->try();
		$_FILES['avatar']['tmp_name'] = __DIR__ . '/fixtures/avatar.png';
		$_FILES['avatar']['error'] = UPLOAD_ERR_OK;
		$_FILES['avatar']['size'] = 100;
		$_FILES['avatar']['name'] = 'avatar.png';
		(new Domain\Upload\Avatars(
			new FileUpload($_FILES['avatar']),
			new Access\FakeUser((string) $user),
			$this->connection,
		))->save();
		$filenameId = (new Storage\TypedQuery($this->connection, 'SELECT avatar_filename_id FROM users WHERE id = ?', [$user]))->field();
		$file = (new Storage\TypedQuery($this->connection, 'SELECT * FROM filesystem.files$images WHERE id = ?', [$filenameId]))->row();
		Assert::match('images/avatars/000/000/000/002/2-%h%.png', $file['filename']);
		@unlink(sprintf(__DIR__ . '/../../../../data/%s', $file['filename']));
	}

	protected function tearDown(): void {
		$this->databaseTeardown();
		copy(__DIR__ . '/fixtures/avatar.sample.png', __DIR__ . '/fixtures/avatar.png');
	}
}

(new AvatarsTest())->run();
