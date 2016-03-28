<?php
namespace Bulletpoint\Model\User;

final class Applicant {
    private $username;
    private $password;
    private $email;

    public function __construct(
        string $username,
        string $password,
        string $email
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
    }

    public function username(): string {
        return $this->username;
    }

    public function password(): string {
        return $this->password;
    }

    public function email(): string {
        return $this->email;
    }
}