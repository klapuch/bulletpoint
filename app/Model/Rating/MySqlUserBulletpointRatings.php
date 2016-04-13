<?php
namespace Bulletpoint\Model\Rating;

final class MySqlUserBulletpointRatings extends Ratings {
    public function iterate(): \Iterator {
        return $this->iterateBy('user_id = ?', [$this->myself->id()]);
    }
}