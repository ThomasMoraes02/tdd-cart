<?php 
namespace Cart\Model;

use Cart\Model\ValueObjects\Email;

class User
{
    private ?int $id;

    private string $name;

    private Email $email;

    public function __construct(string $name, Email $email, ?int $id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }
}