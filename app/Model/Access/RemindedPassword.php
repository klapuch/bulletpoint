<?php
namespace Bulletpoint\Model\Access;

interface RemindedPassword {
    public function change(string $password);
}