<?php
namespace Bulletpoint\Model\Access;

final class NoOneIdentity implements Identity {
    public function id(): int {
        return 0;
    }

    public function role(): Role {
        return Role::DEFAULT_ROLE;
    }

    public function username(): string {
        return '';
    }
}