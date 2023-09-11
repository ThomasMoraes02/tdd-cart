<?php 
namespace Cart\Tests\Unit\Model\Service;

use Exception;
use Cart\Model\Cart;
use Cart\Model\Product\Product;
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

    /**
     * @dataProvider coupon100OFF
     *
     * @return void
     */
    public function testApplyCoupon(array $cupom100OFF): void
    {
        $this->cart->addCoupon($cupom100OFF[0]);

        self::assertEquals(4400.00, $this->cart->getTotal());
        self::assertEquals('100 R$ OFF', $this->cart->getCoupons()[0]['coupon']->getName());
    }

    /**
     * @dataProvider coupon100OFF
     *
     * @param array $cupom100OFF
     * @return void
     */
    public function testApplySameCoupon(array $coupon100OFF): void
    {
        self::expectException(Exception::class);

        $this->cart->addCoupon($coupon100OFF[0]);
        $this->cart->addCoupon($coupon100OFF[0]);
    }

    /**
     * @dataProvider coupon100OFF
     *
     * @param array $coupon100OFF
     * @return void
     */
    public function testRemoveCoupon(array $coupon100OFF): void
    {
        $this->cart->addCoupon($coupon100OFF[0]);
        $this->cart->removeCoupon($coupon100OFF[0]);

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

    public function testApplyCouponsWithDifferentRules()
    {
        $coupon50Of = new Coupon('50OFF', '50% OFF', 50);
        $coupon20PercentOff = new Coupon('20OFF', '20% OFF', 20);
        $coupon20PercentOff->configureRules([
            'type' => 'percentage'
        ]);

        $this->cart->addCoupon($coupon50Of);
        $this->cart->addCoupon($coupon20PercentOff);

        self::assertEquals('50OFF', $this->cart->getCoupons()[0]['coupon']->getCode());
        self::assertEquals('20OFF', $this->cart->getCoupons()[1]['coupon']->getCode());
        self::assertEquals(3560, $this->cart->getTotal());
    }

    public function testRemoveCouponsWithDifferentRules()
    {
        $coupon50Of = new Coupon('50OFF', '50% OFF', 50);
        $coupon20PercentOff = new Coupon('20OFF', '20% OFF', 20);
        $coupon20PercentOff->configureRules([
            'type' => 'percentage'
        ]);

        $this->cart->addCoupon($coupon50Of);
        $this->cart->addCoupon($coupon20PercentOff);

        $this->cart->removeCoupon($coupon50Of);
        $this->cart->removeCoupon($coupon20PercentOff);

        self::assertEquals(4500, $this->cart->getTotal());
    }

    public function testApplyCouponWith100PercentDiscount()
    {
        $coupon100PercentOff = new Coupon('100OFF', '100% OFF', 100);
        $coupon100PercentOff->configureRules([
            'type' => 'percentage'
        ]);

        $this->cart->addCoupon($coupon100PercentOff);

        self::assertEquals(0, $this->cart->getTotal());
    }

    /**
     * Data Provider coupon100OFF
     *
     * @return array
     */
    public function coupon100OFF(): array
    {
        $coupon100 = new Coupon('100OFF', '100 R$ OFF', 100);

        return [
            [
                [$coupon100]
            ]
        ];
    }
}