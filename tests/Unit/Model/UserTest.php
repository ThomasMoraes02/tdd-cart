<?php 
namespace Cart\Tests\Unit\Model;

use Cart\Infra\EncoderArgon2ID;
use Cart\Model\User;
use Cart\Model\ValueObjects\Email;
use Exception;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $encoder = new EncoderArgon2ID();
        $user = new User('Thomas Moraes', new Email('thomas@gmail.com'), $encoder);
        $user->setPassword('123456');

        self::assertTrue($user->checkPassword('123456'));
        self::assertFalse($user->getPassword() == '123456');
        self::assertEquals('Thomas Moraes', $user->getName());
        self::assertEquals('thomas@gmail.com', $user->getEmail());
    }

    public function testUserEmailIsInvalid()
    {
        self::expectException(Exception::class);
        new User('Thomas Moraes', new Email('invalid'), new EncoderArgon2ID());
    }
}