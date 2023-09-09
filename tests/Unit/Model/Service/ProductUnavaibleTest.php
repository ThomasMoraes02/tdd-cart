<?php 
namespace Cart\Tests\Unit\Model\Service;

use Cart\Infra\EncoderArgon2ID;
use Cart\Infra\Factories\UserFactory;
use Cart\Model\Cart;
use Cart\Model\Product;
use PHPUnit\Framework\TestCase;

class ProductUnavaibleTest extends TestCase
{
    private Cart $cart;

    private Product $notebook;

    protected function setUp(): void
    {
        $user = (new UserFactory(new EncoderArgon2ID()))->create('Thomas Moraes', 'thomas@gmail.com', '123456');
        $this->cart = new Cart($user);

        $this->notebook = new Product('Notebook', 2000, 1);
        $this->cart->addProduct($this->notebook);
    }

    public function testSendMailWhenProductUnaivable()
    {
        $this->cart->addProduct($this->notebook);

        self::assertCount(2 , $this->cart->getProducts());
    }
}