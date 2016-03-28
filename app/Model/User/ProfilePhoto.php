<?php
namespace Bulletpoint\Model\User;

use Bulletpoint\Model\{
    Access, Storage, Filesystem
};

final class ProfilePhoto {
    private $owner;
    private $storage;

    public function __construct(
        Access\Identity $owner,
        Filesystem\Storage $storage
    ) {
        $this->owner = $owner;
        $this->storage = $storage;
    }

    public function show(): Filesystem\Image {
        try {
            return new Filesystem\Image(
                $this->storage->load(
                    sprintf('%d.png', $this->owner->id())
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
            sprintf('%d.png', $this->owner->id())
        );
    }
}