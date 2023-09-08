<?php 
namespace Cart\Tests\Unit\Model;

use Exception;
use Cart\Model\Cart;
use Cart\Model\Product;
use Cart\Infra\EncoderArgon2ID;
use PHPUnit\Framework\TestCase;
use Cart\Infra\Factories\UserFactory;
use Cart\Model\Services\Coupon\Coupon;

class CartCouponTest extends TestCase
{
    private Cart $cart;

    protected function setUp(): void
    {
        $userFactory = new UserFactory(new EncoderArgon2ID());
        $user = $userFactory->create('Thomas Moraes', 'thomas@gmail.com', '123456');

        $this->cart = new Cart($user);

        $notebook = new Product('Notebook Dell G15', 4500.00, 3);
        $this->cart->addProduct($notebook);
    }

    public function testApplyCoupon(): void
    {
        $coupon100 = new Coupon('100OFF', '100 R$ OFF', 100);
        $this->cart->addCoupon($coupon100);

        self::assertEquals(4400.00, $this->cart->getTotal());
        self::assertEquals('100 R$ OFF', $this->cart->getCoupons()[0]->getName());
    }

    public function testApplySameCoupon(): void
    {
        self::expectException(Exception::class);

        $coupon100 = new Coupon('100OFF', '100 R$ OFF', 100);
        $this->cart->addCoupon($coupon100);
        $this->cart->addCoupon($coupon100);
    }

    public function testRemoveCoupon()
    {
        $coupon100 = new Coupon('100OFF', '100 R$ OFF', 100);
        $this->cart->addCoupon($coupon100);
        $this->cart->removeCoupon($coupon100);

        self::assertEquals(4500.00, $this->cart->getTotal());
        self::assertEmpty($this->cart->getCoupons());
    }

    public function testApplyCouponWith10PercentDiscount()
    {
        $coupon10percent = new Coupon('10OFF', '10% OFF', 10);
        $coupon10percent->configureRules([
            'type' => 'percentage'
        ]);

        $this->cart->addCoupon($coupon10percent);

        self::assertEquals(4050, $this->cart->getTotal());
    }

    public function testRemoveCouponWith10PercentDiscount()
    {
        $coupon10percent = new Coupon('10OFF', '10% OFF', 10);
        $coupon10percent->configureRules([
            'type' => 'percentage'
        ]);

        $this->cart->addCoupon($coupon10percent);
        $this->cart->removeCoupon($coupon10percent);

        self::assertEquals(4500, $this->cart->getTotal());
    }
}