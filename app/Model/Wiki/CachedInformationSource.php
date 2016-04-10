<?php
namespace Bulletpoint\Model\Wiki;

use Nette\Caching\IStorage;

final class CachedInformationSource implements InformationSource {
    private $origin;
    private $storage;

    public function __construct(InformationSource $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function place(): string {
        return $this->read(__FUNCTION__);
    }

    public function year() {
        return $this->read(__FUNCTION__);
    }

    public function author(): string {
        return $this->read(__FUNCTION__);
    }

    public function edit(string $place, $year, string $author) {
        $this->origin->edit($place, $year, $author);
    }

    private function read(string $method) {
        $key = __CLASS__ . '::' . $method;
        if($this->storage->read($key) === null)
            $this->storage->write($key, $this->origin->$method(), []);
        return $this->storage->read($key);
    }
}