<?php
declare(strict_types = 1);

namespace Bulletpoint\Integration\Domain\Image;

use Bulletpoint\Domain;
use Bulletpoint\Domain\Access;
use Bulletpoint\Fixtures;
use Bulletpoint\TestCase;
use Klapuch\Application;
use Klapuch\Output;
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
		(new Domain\Image\UploadedAvatars(
			new Access\FakeUser((string) $user),
			new Application\FakeRequest(new Output\FakeFormat((string) file_get_contents(__DIR__ . '/fixtures/avatar.png'))),
			$this->connection,
		))->save();
		$filename = (new Storage\TypedQuery($this->connection, 'SELECT avatar_filename FROM users'))->field();
		Assert::true(file_exists(sprintf(__DIR__ . '/../../../../data/%s', $filename)));
		@unlink(sprintf(__DIR__ . '/../../../../data/%s', $filename));
	}
}

(new UploadedAvatarsTest())->run();
