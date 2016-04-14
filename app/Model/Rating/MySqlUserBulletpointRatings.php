<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\Wiki;

final class MySqlUserBulletpointRatings extends Ratings {
    public function iterate(): \Iterator {
        $placeholder = $this->placeholders();
        $rows = array_reverse(
            $this->database->fetchAll(
                sprintf(
                    'SELECT bulletpoint_id,
                    CASE WHEN rating = "+1" THEN 1 ELSE 0 END AS pros,
                    CASE WHEN rating = "-1" THEN 1 ELSE 0 END AS cons
                    FROM bulletpoint_ratings
                    WHERE user_id = ? AND bulletpoint_id IN (%s)',
                    $placeholder->marks
                ),
                array_merge([$this->myself->id()], $placeholder->origins)
            )
        );
        $ratedIds = array_column($rows, 'bulletpoint_id');
        foreach($placeholder->origins as $bulletpointId) {
            $row = current($rows);
            if($row === false || !in_array($bulletpointId, $ratedIds))
                $row = ['pros' => 0, 'cons' => 0];
            else
                next($rows);
            yield new ConstantRating(
                $row['pros'],
                $row['cons'],
                new MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(
                        $bulletpointId,
                        $this->database
                    ),
                    $this->myself,
                    $this->database
                )
            );
        }
    }
}