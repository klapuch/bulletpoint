<?php
namespace Bulletpoint\Model\Constraint;

interface Rule {
    public function isSatisfied($input);
}