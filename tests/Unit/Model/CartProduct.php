<?php 
namespace Cart\Tests\Unit\Model;

use Cart\Infra\EncoderArgon2ID;
use Exception;
use Cart\Model\Cart;
use Cart\Model\User;
use Cart\Model\Product\Product;
use PHPUnit\Framework\TestCase;
use Cart\Model\ValueObjects\Email;

class CartProduct extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        $encoder = new EncoderArgon2ID();
        $user = new User('Thomas Moraes', new Email('thomas@gmail.com'), $encoder);
        $this->cart = new Cart($user);
    }

    /**
     * @dataProvider products
     *
     * @return void
     */
    public function testAddProductCart(array $products): void
    {
        $this->cart->addProduct($products[0])->addProduct($products[1]);

        // Cart
        self::assertEquals(2, $this->cart->getAmount());
        self::assertEquals(6100.00, $this->cart->getTotal());
        self::assertEquals([$products[0], $products[1]], $this->cart->getProducts());

        // Product
        self::assertEquals(2, $products[0]->getQuantity());
        self::assertEquals(1, $products[1]->getQuantity());
    }

    public function testAddProductWhenNoHasInventory(): void
    {
        self::expectException(Exception::class);

        $notebook = new Product('Notebook Dell G15', 4500.00, 0);
        $this->cart->addProduct($notebook);
    }
    
    /**
     * @dataProvider products
     *
     * @return void
     */
    public function testRemoveProduct(array $products): void
    {
        $this->cart->addProduct($products[0])->addProduct($products[1]);
        $this->cart->removeProduct($products[0]);

        self::assertEquals(1, $this->cart->getAmount());
        self::assertEquals(1600.00, $this->cart->getTotal());
    }

    public function testRemoveProductWhenNotFound(): void
    {
        self::expectException(Exception::class);
        $xiomi = new Product('Xiaomi Redmi 10', 1600.00, 2);
        $this->cart->removeProduct($xiomi);
    }

    /**
     * @dataProvider products
     *
     * @return void
     */
    public function testAddProductToCartWithQuantity(array $products): void
    {
        $this->cart->addProduct($products[0], 2);

        self::assertEquals(1, $this->cart->getAmount());
        self::assertEquals(9000, $this->cart->getTotal());
        self::assertEquals(1, $products[0]->getQuantity());
    }

    /**
     * @dataProvider products
     *
     * @return void
     */
    public function testAddProductToCartWhenNoHasQuantity(array $products): void
    {
        $this->expectException(Exception::class);
        $this->cart->addProduct($products[0], 4);
    }

     /**
     * @dataProvider products
     *
     * @return void
     */
    public function testRemoveProductToCartWithQuantity(array $products): void
    {
        $this->cart->addProduct($products[0], 2);
        $this->cart->removeProduct($products[0], 1);

        self::assertEquals(1, $this->cart->getAmount());
        self::assertEquals(4500, $this->cart->getTotal());
        self::assertEquals(2, $products[0]->getQuantity());

        $this->cart->removeProduct($products[0], 10);
        
        self::assertEquals(0, $this->cart->getAmount());
        self::assertEquals(0, $this->cart->getTotal());
        self::assertEquals(3, $products[0]->getQuantity());
    }

    /**
     * Product Data Provider
     *
     * @return void
     */
    public function products(): array
    {
        $notebook = new Product('Notebook Dell G15', 4500.00, 3);
        $xiomi = new Product('Xiaomi Redmi 10', 1600.00, 2);

        return [
            [
                [$notebook, $xiomi]
            ]
        ];
    }
}