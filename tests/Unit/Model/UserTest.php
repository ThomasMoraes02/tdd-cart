<?php 
namespace Cart\Tests\Unit\Model;

use Cart\Infra\EncoderArgon2ID;
use Cart\Infra\Factories\UserFactory;
use Cart\Model\User;
use Cart\Model\ValueObjects\Email;
use Cart\Model\ValueObjects\Phone;
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

    public function testUserPhone(): void
    {
        $user = (new UserFactory(new EncoderArgon2ID()))->create('Thomas Moraes', 'thomas@gmail.com', '123456');
        $user->addPhone(new Phone('11', '999999999'));

        self::assertEquals('999999999', $user->getPhone()->getNumber());
        self::assertEquals('11', $user->getPhone()->getAreaCode());
        self::assertSame('11999999999', $user->getPhone()->__toString());
    }
}