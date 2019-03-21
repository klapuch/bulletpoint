<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Image;

use Bulletpoint\Domain\Access;

final class UploadedAvatars implements Avatars {
	private const PATH = __DIR__ . '/../../../data/images';

	/**
	 * @var Access\User
	 */
	private $user;

	public function __construct(Access\User $user) {
		$this->user = $user;
	}

	public function save(): void {
		move_uploaded_file($_FILES['avatar']['tmp_name'], self::PATH . DIRECTORY_SEPARATOR . sprintf('__avatar_%d.png', $this->user->id()));
	}
}
