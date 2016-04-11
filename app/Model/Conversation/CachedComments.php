<?php
namespace Bulletpoint\Model\Conversation;

use Nette\Caching\IStorage;

final class CachedComments implements Comments {
    private $origin;
    private $storage;

    public function __construct(Comments $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function iterate(): \Iterator {
        $method = __FUNCTION__;
        $key = __CLASS__ . '::' . $method;
        if($this->storage->read($key) === null) {
            $this->storage->write(
                $key,
                iterator_to_array($this->origin->$method()),
                []
            );
        }
        return new \ArrayIterator($this->storage->read($key));
    }

    public function count(): int {
        return $this->read(__FUNCTION__);
    }

    private function read(string $method) {
        $key = __CLASS__ . '::' . $method;
        if($this->storage->read($key) === null)
            $this->storage->write($key, $this->origin->$method(), []);
        return $this->storage->read($key);
    }
}