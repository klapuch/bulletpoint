<?php
namespace Bulletpoint\Model\Filesystem;

use Bulletpoint\Exception;

final class Image {
    private $file;
    const CREATE = 0;
    const STORE = 1;
    const EXTENSION = 2;
    const ALLOWED_EXTENSIONS = [
        'image/jpeg' => [
            self::CREATE => 'imagecreatefromjpeg',
            self::STORE => 'imagejpeg',
            self::EXTENSION => 'jpg',
        ],
        'image/png' => [
            self::CREATE => 'imagecreatefrompng',
            self::STORE => 'imagepng',
            self::EXTENSION => 'png',
        ],
        'image/gif' => [
            self::CREATE => 'imagecreatefromgif',
            self::STORE => 'imagegif',
            self::EXTENSION => 'gif',
        ],
    ];

    public function __construct(File $file) {
        if(!in_array($file->type(), array_keys(self::ALLOWED_EXTENSIONS), true)) {
            throw new Exception\StorageException(
                sprintf(
                    'Soubor typu %s není obrázek. Povolené typy jsou %s',
                    $this->toReadableExtension([$file->type()]),
                    $this->toReadableExtension(array_keys(self::ALLOWED_EXTENSIONS))
                )
            );
        }
        $this->file = $file;
    }

    public function resize(Size $newSize): self {
        if((string)$newSize === (string)$this->size())
            return clone $this;
        $currentSize = $this->size();
        list($width, $height) = [$currentSize->width(), $currentSize->height()];
        $type = self::ALLOWED_EXTENSIONS[$this->file->type()];
        $source = $type[self::CREATE]($this->file->location());
        $image = imagecreatetruecolor($newSize->width(), $newSize->height());
        imagecopyresampled(
            $image,
            $source,
            0,
            0,
            0,
            0,
            $newSize->width(),
            $newSize->height(),
            $width,
            $height
        );
        $tempFile = tempnam(sys_get_temp_dir(), $type[self::EXTENSION]);
        $type[self::STORE]($image, $tempFile);
        return new self(new SavedFile(new StandardizedPath($tempFile)));
    }

    public function asFile(): File {
        return $this->file;
    }

    public function size(): Size {
        list($width, $height) = getimagesize($this->file->location());
        return new Size(
            $width,
            $height
        );
    }

    private function toReadableExtension(array $extensions): string {
        return rtrim(
            (string)array_reduce(
                $extensions,
                function($previous, $extension) {
                    return $previous .= explode('/', $extension)[1] . ', ';
                }
            ),
            ', '
        );
    }
}