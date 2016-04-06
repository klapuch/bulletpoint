<?php
namespace Bulletpoint\Model\Rating;

final class MySqlUserBulletpointRatings extends Ratings {
    public function iterate(): \Iterator {
        if($this->myself->id())
            return $this->iterateBy('user_id = ?', [$this->myself->id()]);
        return new \ArrayIterator(
            array_fill(
                0,
                iterator_count($this->bulletpoints->iterate()),
                new InvalidRating
            )
        );
    }
}