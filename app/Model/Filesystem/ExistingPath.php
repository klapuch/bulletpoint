<?php
namespace Bulletpoint\Model\Filesystem;

final class ExistingPath implements Path {
    private $path;

    public function __construct(Path $path) {
        $this->path = $path;
    }

    public function folder(): string {
        return $this->path->folder();
    }

    public function file(): string {
        if($this->path->file())
            $this->checkFile();
        return $this->path->file();
    }

    public function extension(): string {
        return $this->path->extension();
    }

    public function full(): string {
        return $this->folder() . $this->file() . $this->extension();
    }

    private function checkFile() {
        if(!is_file($this->path->full())) {
            throw new \RuntimeException(
                sprintf(
                    '%s does not exist',
                    $this->path->file() . $this->path->extension()
                )
            );
        }
    }
}