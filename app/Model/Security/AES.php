<?php
namespace Bulletpoint\Model\Security;

/**
 * Strongly immutable class
 */
abstract class AES {
    private $key;

    final public function __construct(string $key) {
        $this->key = $key;
    }

    final protected function key(): string {
        return $this->key;
    }
}