<?php
namespace Bulletpoint\Model\Report;

use Nette\Caching\IStorage;

final class CachedComplaints implements Complaints {
    private $origin;
    private $storage;

    public function __construct(Complaints $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function iterate(Target $target = null): \Iterator {
        $key = __CLASS__ . '::' . __FUNCTION__;
        if($this->storage->read($key) === null) {
            $this->storage->write(
                $key,
                iterator_to_array($this->origin->iterate($target)),
                []
            );
        }
        return new \ArrayIterator($this->storage->read($key));
    }

    public function complain(Target $target, string $reason): Complaint {
        return $this->origin->complain($target, $reason);
    }

    public function settle(Target $target) {
        $this->origin->settle($target);
    }
}