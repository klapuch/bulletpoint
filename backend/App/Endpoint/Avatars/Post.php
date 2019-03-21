<?php
declare(strict_types = 1);

namespace Bulletpoint\Endpoint\Avatars;

use Bulletpoint\Domain\Access;
use Bulletpoint\Domain\Image;
use Klapuch\Application;
use Klapuch\Storage;

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
		(new Image\UploadedAvatars($this->user))->save();
		return new Application\EmptyResponse();
	}
}
