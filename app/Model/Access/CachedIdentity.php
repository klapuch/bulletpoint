<?php
namespace Bulletpoint\Model\Access;

use Nette\Caching\IStorage;

final class CachedIdentity implements Identity {
    private $origin;
    private $storage;

    public function __construct(Identity $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function role(): Role {
        return $this->read(__FUNCTION__);
    }

    public function username(): string {
        return $this->read(__FUNCTION__);
    }

    private function read(string $method) {
        $key = __CLASS__ . '::' . $method;
        if($this->storage->read($key) === null)
            $this->storage->write($key, $this->origin->$method(), []);
        return $this->storage->read($key);
    }
}