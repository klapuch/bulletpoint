<?php
namespace Bulletpoint\Model\Filesystem;

final class StandardizedPath implements Path {
    private $path;
    const SEPARATORS = '\/';

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function folder(): string {
        return rtrim(
            (string)pathinfo($this->path, PATHINFO_DIRNAME),
            self::SEPARATORS
        ) . '/';
    }

    public function file(): string {
        return trim(
            (string)pathinfo($this->path, PATHINFO_FILENAME),
            self::SEPARATORS
        );
    }

    public function extension(): string {
        $extension = (string)pathinfo($this->path, PATHINFO_EXTENSION);
        if($extension)
            return '.' . trim($extension, '.');
        return $extension;
    }

    public function full(): string {
        return $this->folder() . $this->file() . $this->extension();
    }
}