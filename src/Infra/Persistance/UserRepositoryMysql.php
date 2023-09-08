<?php 
namespace Cart\Infra\Persistance;

use Cart\Infra\Factories\UserFactory;
use PDO;
use Cart\Model\Repository\UserRepository;
use Cart\Model\User;
use Cart\Model\ValueObjects\Email;

class UserRepositoryMysql implements UserRepository
{
    private PDO $pdo;

    private UserFactory $userFactory;

    public function __construct(PDO $pdo, UserFactory $userFactory)
    {
        $this->pdo = $pdo;
        $this->userFactory = $userFactory;
    }

    public function save(User $user): User
    {
        $sql = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('name', $user->getName());
        $stmt->bindValue('email', $user->getEmail());
        $stmt->bindValue('password', $user->getPassword());
        $stmt->execute();

        return $this->userFactory->create(
            $user->getName(), 
            $user->getEmail(), 
            $user->getPassword(), 
            $this->pdo->lastInsertId()
        );
    }

    public function findByEmail(Email $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('email', strval($email));
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        return $this->userFactory->create(
            $user['name'],
            $user['email'],
            $user['password'],
            $user['id']
        );
    }
}