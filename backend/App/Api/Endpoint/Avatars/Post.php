<?php
declare(strict_types = 1);

namespace Bulletpoint\Api\Endpoint\Avatars;

use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Upload;
use Klapuch\Application;
use Klapuch\Storage;
use Nette\Http\FileUpload;

final class Post implements Application\View {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Bulletpoint\Domain\Access\User */
	private $user;

	public function __construct(Storage\Connection $connection, Access\User $user) {
		$this->connection = $connection;
		$this->user = $user;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function response(array $parameters): Application\Response {
		(new Upload\Avatars(new FileUpload($_FILES['avatar']), $this->user, $this->connection))->save();
		return new Application\EmptyResponse();
	}
}
