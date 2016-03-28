<?php
namespace Bulletpoint\Model\Security;

final class FilesystemSession implements \SessionHandlerInterface {
    private $path;
    private $key;

    public function __construct(string $key) {
        $this->key = $key;
    }

    public function open($path, $name): bool {
        $this->path = $path ?: sys_get_temp_dir();
        return true;
    }

    public function close(): bool {
        return true;
    }

    public function read($id): string {
        if(is_file($this->filename($id)))
            return (string)file_get_contents($this->filename($id));
        return '';
    }

    public function write($id, $data): bool {
        return (bool)file_put_contents($this->filename($id), $data);
    }

    public function destroy($id): bool {
        if(is_file($this->filename($id)))
            return unlink($this->filename($id));
        return false;
    }

    public function gc($maxLifeTime): bool {
        foreach(glob("$this->path/sess_*") as $file)
            if(is_file($file) && filemtime($file) + $maxLifeTime < time())
                unlink($file);
        return true;
    }

    private function filename(string $id): string {
        return sprintf('%s/sess_%s', $this->path, md5($id . $this->key));
    }
}