<?php
namespace Bulletpoint\Model\Access;

interface Identity {
    const ID = 'id';
    const ROLE = 'role';
    const USERNAME = 'username';
    public function id(): int;
    public function role(): Role;
    public function username(): string;
}