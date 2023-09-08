<?php 
namespace Cart\Model\Repository;

use Cart\Model\User;
use Cart\Model\ValueObjects\Email;

interface UserRepository
{
    public function save(User $user): User;

    public function findByEmail(Email $email): ?User;
}