<?php 
namespace Cart\Infra\Factories;

use Cart\Model\User;
use Cart\Model\Encoder;
use Cart\Model\ValueObjects\Email;

class UserFactory
{
    private Encoder $encoder;

    public function __construct(Encoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Cria um Usuário
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @param int|null $id
     * @return User
     */
    public function create(string $name, string $email, string $password, ?int $id = null): User
    {
        $user = new User($name, new Email($email), $this->encoder, $id);
        $user->setPassword($password);
        return $user;
    }
}