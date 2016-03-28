<?php
namespace Bulletpoint\Model\Access;

final class ConstantRole implements Role {
    private $currentRole;
    private $origin;

    public function __construct(string $currentRole, Role $origin) {
        $this->currentRole = $currentRole;
        $this->origin = $origin;
    }

    public function degrade(): Role {
        return $this->origin->degrade();
    }

    public function promote(): Role {
        return $this->origin->promote();
    }

    public function rank(): int {
        return $this->origin->rank();
    }

    public function __toString() {
        return $this->currentRole ?: self::DEFAULT_ROLE;
    }
}