<?php
namespace Bulletpoint\Model\Rating;

final class MySqlBulletpointRatings extends Ratings {
    public function iterate(): \Iterator {
        return $this->iterateBy();
    }
}