<?php 
namespace Cart\Model;

use Cart\Model\Encoder;
use Cart\Model\ValueObjects\Email;

class User
{
    private ?int $id;

    private string $name;

    private Email $email;

    private Encoder $encoder;

    private string $password;

    public function __construct(string $name, Email $email, Encoder $encoder, ?int $id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->encoder = $encoder;
        $this->password = '';
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

    public function setPassword(string $password): void
    {
        $this->password = $this->encoder->encode($password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->encoder->decode($password, $this->password);
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}