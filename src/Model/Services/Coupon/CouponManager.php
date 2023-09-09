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
        foreach($this->coupons as $cartCoupon) {
            if($cartCoupon->getCode() == $coupon->getCode()) {
                throw new Exception("O cupom {$coupon->getCode()} já foi aplicado.");
            }
        }

        $this->coupons[] = $coupon;
        return $this->cart->getTotal() - $this->calculateRule($coupon, $this->cart->getTotal());
    }

    /**
     * Remove coupon
     *
     * @param Coupon $coupon
     * @return float
     */
    public function removeCoupon(Coupon $coupon): float
    {
        foreach($this->coupons as $couponKey => $couponCart) {
            if($couponCart == $coupon) {
                unset($this->coupons[$couponKey]);
                return $this->cart->getTotal() + $this->calculateRule($coupon, $this->cart->getSubtotal());
                break;
            }
        }
    }

    /**
     * Calculate rule
     *
     * @param Coupon $coupon
     * @return float
     */
    private function calculateRule(Coupon $coupon, float $baseValue = 0): float
    {
        switch ($coupon->getType()) {
            case CouponTypes::PERCENTAGE:
                return ($baseValue * $coupon->getValue() / 100);
                break;
            
            default:
                return $coupon->getValue();
                break;
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