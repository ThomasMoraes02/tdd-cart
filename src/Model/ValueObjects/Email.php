<?php 
namespace Cart\Model\ValueObjects;

use Exception;

class Email
{
    private string $email;
    
    public function __construct(string $email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('O e-mail informado é inválido');
        }
        
        $this->email = $email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}