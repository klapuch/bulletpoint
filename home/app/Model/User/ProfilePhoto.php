<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Core\{Storage, Filesystem};
use Bulletpoint\Model\{Access};

final class ProfilePhoto implements Photo {
	private $myself;
	private $storage;

	public function __construct(
		Access\Identity $myself,
		Filesystem\Storage $storage
	) {
		$this->myself = $myself;
		$this->storage = $storage;
	}

	public function show(): Filesystem\Image {
		try {
			return new Filesystem\Image(
				$this->storage->load(
					sprintf('%d.png', $this->myself->id())
				)
			);
		} catch(\RuntimeException $ex) {
			return new Filesystem\Image(
				$this->storage->load('default/profile.png')
			);
		}
	}

	public function change(Filesystem\Image $image) {
		$resizedImage = $image->resize(new Filesystem\Size(64, 64));
		$this->storage->save(
			$resizedImage->asFile(),
			sprintf('%d.png', $this->myself->id())
		);
	}
}