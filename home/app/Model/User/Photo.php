<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Core\{Filesystem};

interface Photo {
	public function show(): Filesystem\Image;
	public function change(Filesystem\Image $image);
}