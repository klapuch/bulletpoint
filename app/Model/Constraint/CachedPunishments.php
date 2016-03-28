<?php
namespace Bulletpoint\Model\Constraint;

use Nette\Caching\IStorage;

final class CachedPunishments extends Punishments {
    private $origin;
    private $storage;

    public function __construct(Punishments $origin, IStorage $storage) {
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
}