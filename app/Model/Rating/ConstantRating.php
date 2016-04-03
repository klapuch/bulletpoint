<?php
namespace Bulletpoint\Model\Rating;

final class ConstantRating implements Rating {
    private $pros;
    private $cons;
    private $origin;

    public function __construct(int $pros, int $cons, Rating $origin) {
        $this->pros = $pros;
        $this->cons = $cons;
        $this->origin = $origin;
    }

    public function increase() {
        $this->origin->increase();
    }

    public function decrease() {
        $this->origin->decrease();
    }

    public function pros(): int {
        return $this->pros;
    }

    public function cons(): int {
        return $this->cons;
    }
}