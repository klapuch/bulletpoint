<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\Wiki;

final class MySqlBulletpointRatings extends Ratings {
    public function iterate(): \Iterator {
        $placeholder = $this->placeholders();
        $rows = array_reverse(
            $this->database->fetchAll(
                sprintf(
                    'SELECT
                    SUM(CASE WHEN rating = "+1" THEN 1 ELSE 0 END) AS pros,
                    SUM(CASE WHEN rating = "-1" THEN 1 ELSE 0 END) AS cons
                    FROM bulletpoint_ratings
                    WHERE bulletpoint_id IN (%s)
                    GROUP BY bulletpoint_id',
                    $placeholder->marks
                ),
                $placeholder->origins
            )
        );
        foreach($placeholder->origins as $bulletpointId) {
            $row = current($rows);
            yield new ConstantRating(
                (int)$row['pros'],
                (int)$row['cons'],
                new MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(
                        $bulletpointId,
                        $this->database
                    ),
                    $this->myself,
                    $this->database
                )
            );
            next($rows);
        }
    }
}