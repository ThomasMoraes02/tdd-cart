<?php 
namespace Cart\Model\Services\Coupon;

use Exception;
use Cart\Model\Cart;

class CouponManager
{
    private float $cartTotal = 0;

    /** @var Coupon[] */
    private array $coupons = [];

    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Apply coupon to cart
     *
     * @param Coupon $coupon
     * @return float
     */
    public function applyCouponToCart(Coupon $coupon): float
    {
        foreach($this->coupons as $coupon) {
            if($coupon->getCode() == $coupon->getCode()) {
                throw new Exception("O cupom {$coupon->getCode()} já foi aplicado.");
            }
        }

        $this->coupons[] = $coupon;
        $cartTotal = $this->cart->getTotal() - $coupon->getValue();

        return $cartTotal;
    }

    /**
     * Remove coupon
     *
     * @param Coupon $coupon
     * @return float
     */
    public function removeCouponToCart(Coupon $coupon): float
    {
        foreach($this->coupons as $couponKey => $couponCart) {
            if($couponCart == $coupon) {
                unset($this->coupons[$couponKey]);
                $this->cartTotal = $this->cart->getTotal() + $coupon->getValue();
                break;
            }
        }

        return $this->cartTotal;
    }

    public function getCoupons(): array
    {
        return $this->coupons;
    }
}