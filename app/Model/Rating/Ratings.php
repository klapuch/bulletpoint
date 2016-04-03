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
        if($condition !== null)
            $condition .= ' AND';
        $placeholders = implode(
            ',',
            array_fill(
                0,
                iterator_count($this->bulletpoints->iterate()),
                '?'
            )
        );
        $bulletpoints = (array)array_reduce(
            iterator_to_array($this->bulletpoints->iterate()),
            function($previous, Wiki\Bulletpoint $current) {
                $previous[] = $current->id();
                return $previous;
            }
        );
        $rows = $this->database->fetchAll(
            "SELECT bulletpoint_id,
            SUM(CASE WHEN rating = \"+1\" THEN 1 ELSE 0 END) AS pros,
            SUM(CASE WHEN rating = \"-1\" THEN 1 ELSE 0 END) AS cons
            FROM bulletpoint_ratings
            WHERE $condition bulletpoint_id IN ($placeholders)
            GROUP BY bulletpoint_id",
            array_merge($parameters, $bulletpoints)
        );
        foreach(array_reverse($rows) as $row) {
            yield new ConstantRating(
                (int)$row['pros'],
                (int)$row['cons'],
                new MySqlBulletpointRating(
                    new Wiki\MySqlBulletpoint(
                        $row['bulletpoint_id'],
                        $this->database
                    ),
                    $this->myself,
                    $this->database
                )
            );
        }
    }
}