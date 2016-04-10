<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;
use Nette\Caching\IStorage;

final class CachedDocument implements Document {
    private $origin;
    private $storage;

    public function __construct(Document $origin, IStorage $storage) {
        $this->origin = $origin;
        $this->storage = $storage;
    }

    public function author(): Access\Identity {
        return new Access\CachedIdentity(
            $this->read(__FUNCTION__),
            $this->storage
        );
    }

    public function title(): string {
        return $this->read(__FUNCTION__);
    }

    public function description(): string {
        return $this->read(__FUNCTION__);
    }

    public function source(): InformationSource {
        return new CachedInformationSource(
            $this->read(__FUNCTION__),
            $this->storage
        );
    }

    public function id(): int {
        return $this->origin->id();
    }

    public function date(): \DateTime {
        return $this->read(__FUNCTION__);
    }

    public function edit(string $title, string $description) {
        $this->origin->edit($title, $description);
    }

    private function read(string $method) {
        $key = __CLASS__ . '::' . $method;
        if($this->storage->read($key) === null)
            $this->storage->write($key, $this->origin->$method(), []);
        return $this->storage->read($key);
    }
}