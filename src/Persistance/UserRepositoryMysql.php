<?php 
namespace Cart\Persistance;

use PDO;
use Cart\Model\Repository\UserRepository;
use Cart\Model\User;
use Cart\Model\ValueObjects\Email;

class UserRepositoryMysql implements UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(User $user): User
    {
        $sql = 'INSERT INTO users (name, email) VALUES (:name, :email)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('name', $user->getName());
        $stmt->bindValue('email', $user->getEmail());
        $stmt->execute();

        return new User($user->getName(), new Email($user->getEmail()), $this->pdo->lastInsertId());
    }

    public function findByEmail(Email $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('email', strval($email));
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? new User($user['name'], new Email($user['email']), $user['id']) : null;
    }
}