<?php
namespace Bulletpoint\Model\Report;

final class Target {
    private $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public function id(): int {
        return $this->id;
    }
}