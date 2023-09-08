<?php 
namespace Cart\Tests\Unit\Infra;

use Exception;
use Cart\Infra\EncoderArgon2ID;
use PHPUnit\Framework\TestCase;

class EncoderArgon2IDTest extends TestCase
{
    private EncoderArgon2ID $encoder;

    protected function setUp(): void
    {
        $this->encoder = new EncoderArgon2ID();   
    }

    public function testEncodePassword(): void
    {
        $password = '123456';

        $encoded = $this->encoder->encode($password);
        $encodedAgain = $this->encoder->encode($password);

        $decoded = $this->encoder->decode($password, $encoded);
        $decodedAgain = $this->encoder->decode($password, $encodedAgain);

        self::assertIsString($encoded);
        self::assertTrue($decoded);
        self::assertTrue($decodedAgain);
    }

    public function testPasswordIsNotSame()
    {
        $password = '12345678';
        $encoded = $this->encoder->encode($password);

        $decoded = $this->encoder->decode('123456', $encoded);

        self::assertFalse($decoded);
    }

    public function testPasswordHasMin6Characters()
    {
        self::expectException(Exception::class);
        $this->encoder->encode('12345');
    }
}