<?php
namespace Bulletpoint\Model\Filesystem;

use Bulletpoint\Exception;

final class UploadedFile implements File {
    private $name;
    private $size;
    private $error;
    private $temporaryFile;

    public function __construct(array $file) {
        foreach(['name', 'type', 'size', 'error', 'tmp_name'] as $key) {
            if(!isset($file[$key])) {
                throw new \InvalidArgumentException(
                    sprintf('File does not include %s key', $key)
                );
            }
        }
        $this->name = $file['name'];
        $this->size = $file['size'];
        $this->error = $file['error'];
        $this->temporaryFile = $file['tmp_name'];
    }

    public function name(): string {
        return preg_replace(
            '~[^a-z0-9_-]~i',
            '_',
            mb_substr($this->name, 0, 225)
        );
    }

    public function type(): string {
        if($this->isOk()) {
            return finfo_file(
                finfo_open(FILEINFO_MIME_TYPE),
                $this->location()
            );
        }
        throw new Exception\UploadException($this->error());
    }

    public function size(): int {
        return (int)$this->size;
    }

    public function content(): string {
        if(is_uploaded_file($this->location()) && $this->isOk())
            return file_get_contents($this->location());
        throw new Exception\UploadException($this->error());
    }

    public function location(): string {
        return $this->temporaryFile;
    }

    public function error(): int {
        return $this->error;
    }

    private function isOk(): bool {
        return $this->error() === UPLOAD_ERR_OK;
    }
}