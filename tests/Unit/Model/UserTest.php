<?php 
namespace Cart\Tests\Unit\Model;

use Cart\Model\User;
use Cart\Model\ValueObjects\Email;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $user = new User('Thomas Moraes', new Email('thomas@gmail.com'));

        self::assertEquals('Thomas Moraes', $user->getName());
        self::assertEquals('thomas@gmail.com', $user->getEmail());
    }
}