<?php 
namespace Cart\Infra\Persistance;

use Cart\Infra\Factories\UserFactory;
use PDO;
use Cart\Model\Repository\UserRepository;
use Cart\Model\User;
use Cart\Model\ValueObjects\Email;
use Cart\Model\ValueObjects\Phone;

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
        $sql = 'INSERT INTO users (name, email, password, phone_area, phone_number) VALUES (:name, :email, :password, :phone_area, :phone_number);';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('name', $user->getName());
        $stmt->bindValue('email', $user->getEmail());
        $stmt->bindValue('password', $user->getPassword());

        if($user->getPhone()) {
            $stmt->bindValue('phone_area', $user->getPhone()->getAreaCode());
            $stmt->bindValue('phone_number', $user->getPhone()->getNumber());
        }

        $stmt->execute();

        $user = $this->userFactory->create(
            $user->getName(), 
            $user->getEmail(), 
            $user->getPassword(), 
            $this->pdo->lastInsertId()
        );

        if($user->getPhone()) {
            $user->addPhone(new Phone($user->getPhone()->getAreaCode(), $user->getPhone()->getNumber()));
        }

        return $user;
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

        $user = $this->userFactory->create(
            $user['name'],
            $user['email'],
            $user['password'],
            $user['id']
        );

        if($user->getPhone()) {
            $user->addPhone(new Phone($user->getPhone()->getAreaCode(), $user->getPhone()->getNumber()));
        }

        return $user;
    }
}