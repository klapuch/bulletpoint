<?php
namespace Bulletpoint\Model\Wiki;

use Nette\Caching\IStorage;

final class CachedBulletpoints extends Bulletpoints {
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

    public function add(string $content, InformationSource $source) {
        $this->origin->add($content, $source);
    }
}