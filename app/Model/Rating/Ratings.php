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
    protected function placeholders(): \stdClass {
        $origins = [];
        foreach($this->bulletpoints->iterate() as $bulletpoint)
            $origins[] = $bulletpoint->id();
        $placeholder = implode(
            ',',
            array_fill(0, count($origins), '?')
        );
        return (object)[
            'origins' => array_reverse($origins),
            'marks' => $placeholder
        ];
    }
}