<?php
namespace Bulletpoint\Model\Rating;

final class InvalidRating implements Rating {
    public function increase() {
        throw new \LogicException(
            'Neplatné hodnocení nemůže být nadále zvyšováno'
        );
    }

    public function decrease() {
        throw new \LogicException(
            'Neplatné hodnocení nemůže být nadále snižováno'
        );
    }

    public function pros(): int {
        return 0;
    }

    public function cons(): int {
        return 0;
    }
}