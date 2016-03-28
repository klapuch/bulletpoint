<?php
namespace Bulletpoint\Model\Conversation;

use Nette\Caching\IStorage;

final class CachedDiscussion implements Discussion {
    private $origin;
    private $storage;

    public function __construct(Discussion $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function post(string $content) {
        $this->origin->post($content);
    }

    public function comments(): \Iterator {
        $key = __CLASS__ . '::' . __FUNCTION__;
        if($this->storage->read($key) === null) {
            $this->storage->write(
                $key,
                iterator_to_array($this->origin->comments()),
                []
            );
        }
        return new \ArrayIterator($this->storage->read($key));
    }
}