<?php 
namespace Cart\Model\Services\Coupon;

use Exception;
use Cart\Model\Cart;

class CouponManager
{
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
    public function applyCoupon(Coupon $coupon): float
    {
        foreach($this->coupons as $couponCart) {
            if($couponCart['coupon']->getCode() == $coupon->getCode()) {
                throw new Exception("O cupom {$coupon->getCode()} já foi aplicado.");
            }
        }

        $discount = $this->calculateRule($coupon, $this->cart->getTotal());
        $this->coupons[] = ['coupon' => $coupon, 'discount' => $discount];

        return $this->cart->getTotal() - $discount;
    }

    /**
     * Remove coupon
     *
     * @param Coupon $coupon
     * @return float
     */
    public function removeCoupon(Coupon $coupon): float
    {
        foreach($this->coupons as $key => $couponCart) {
            if($couponCart['coupon'] == $coupon) {
                unset($this->coupons[$key]);
                return $this->cart->getTotal() + $couponCart['discount'];
            }
        }
    }

    /**
     * Calculate rule
     *
     * @param Coupon $coupon
     * @return float
     */
    private function calculateRule(Coupon $coupon): float
    {
        switch ($coupon->getType()) {
            case CouponTypes::PERCENTAGE:
                return ($this->cart->getTotal() * $coupon->getValue() / 100);
                break;

            case CouponTypes::FIXED:
                return $coupon->getValue();
        }
    }

    /**
     * Get All Coupons
     *
     * @return array
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }
}