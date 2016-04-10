<?php
namespace Bulletpoint\Model\Wiki;

use Nette\Caching\IStorage;

final class CachedBulletpoints implements Bulletpoints {
    private $origin;
    private $storage;

    public function __construct(Bulletpoints $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function iterate(): \Iterator {
        $key = __CLASS__ . '::' . __FUNCTION__;
        if($this->storage->read($key) === null) {
            $this->storage->write(
                $key,
                iterator_to_array($this->origin->iterate()),
                []
            );
        }
        return new \ArrayIterator($this->storage->read($key));
    }

    public function add(
        string $content,
        Document $document,
        InformationSource $source
    ) {
        $this->origin->add($content, $document, $source);
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