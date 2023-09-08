<?php 
namespace Cart\Model\Services\Coupon;

use Exception;
use Cart\Model\Cart;

class CouponManager
{
    private float $cartSubtotal = 0;

    /** @var Coupon[] */
    private array $coupons = [];

    private Cart $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        $this->cartSubtotal = $this->cart->getSubtotal();
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
        return $this->cart->getSubtotal() - $this->calculateRule($coupon);
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
                $this->cartSubtotal = $this->cart->getTotal() + $this->calculateRule($coupon);
                break;
            }
        }

        return $this->cartSubtotal;
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
                return ($this->cart->getSubtotal() * $coupon->getValue() / 100);
                break;
            
            default:
                return $coupon->getValue();
                break;
        }
    }

    public function getCoupons(): array
    {
        return $this->coupons;
    }
}