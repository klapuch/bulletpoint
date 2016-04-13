<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Storage, Wiki, Access
};

abstract class Ratings {
    protected $bulletpoints;
    protected $myself;
    protected $database;

    public function __construct(
        Wiki\Bulletpoints $bulletpoints,
        Access\Identity $myself,
        Storage\Database $database
    ) {
        $this->bulletpoints = $bulletpoints;
        $this->myself = $myself;
        $this->database = $database;
    }
    
    public abstract function iterate(): \Iterator;
    protected function iterateBy(
        string $condition = null,
        array $parameters = []
    ): \Iterator {
        $placeholders = implode(
            ',',
            array_fill(0, $this->bulletpoints->count(), '?')
        );
        $bulletpointIds = (array)array_reduce(
            iterator_to_array($this->bulletpoints->iterate()),
            function($previous, Wiki\Bulletpoint $current) {
                $previous[] = $current->id();
                return $previous;
            }
        );
        $rows = array_reverse(
            (array)$this->database->fetchAll(
                sprintf(
                    'SELECT bulletpoint_id,
                    SUM(CASE WHEN rating = "+1" THEN 1 ELSE 0 END) AS pros,
                    SUM(CASE WHEN rating = "-1" THEN 1 ELSE 0 END) AS cons
                    FROM bulletpoint_ratings
                    WHERE %s bulletpoint_id IN (%s)
                    GROUP BY bulletpoint_id',
                    $condition ? $condition . ' AND' : $condition,
                    $placeholders
                ),
                array_merge($parameters, $bulletpointIds)
            )
        );
        foreach(array_reverse($bulletpointIds) as $bulletpoint) {
            $row = current($rows);
            yield new ConstantRating(
                (int)$row['pros'],
                (int)$row['cons'],
                new MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(
                        $bulletpoint,
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