<?php
namespace Bulletpoint\Model\Rating;

use Bulletpoint\Model\{
    Storage, Wiki, Access
};

abstract class Ratings {
    protected $bulletpoints;

    public function __construct(Wiki\Bulletpoints $bulletpoints) {
        $this->bulletpoints = $bulletpoints;
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