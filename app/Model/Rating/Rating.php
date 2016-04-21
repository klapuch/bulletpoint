<?php
namespace Bulletpoint\Model\Rating;

interface Rating {
    public function increase();
    public function decrease();
    public function points(): \Iterator;
}