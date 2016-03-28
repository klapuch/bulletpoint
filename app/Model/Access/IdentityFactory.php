<?php
namespace Bulletpoint\Model\Access;

use Bulletpoint\Model\Storage;
use Nette\Security;

final class IdentityFactory {
    private $user;
    private $database;

    public function __construct(
        Security\User $user,
        Storage\Database $database
    ) {
        $this->user = $user;
        $this->database = $database;
    }

    /**
     * @return Identity
     */
    public function create(): Identity {
        if($this->user->identity === null)
            return new NoOneIdentity();
        return new ConstantIdentity(
            $this->user->id,
            new ConstantRole(
                current($this->user->identity->roles),
                new MySqlRole($this->user->id, $this->database)
            ),
            $this->user->identity->username ??
            (new MySqlIdentity($this->user->id, $this->database))->username()
        );
    }
}