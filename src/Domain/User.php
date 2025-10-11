<?php
namespace OpenBook\Domain;

class User
{
    public ?int $id;
    public string $name;
    public string $email;
    public string $passwordHash;

    public function __construct(?int $id, string $name, string $email, string $passwordHash)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }
}
