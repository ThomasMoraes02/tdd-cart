<?php 
namespace Cart\Model;

use Exception;
use Cart\Model\User;
use Cart\Model\Product;
use Cart\Model\Services\Coupon\Coupon;

class Cart
{
    private ?int $id;

    private User $user;

    /** @var Product[] */
    private array $products = [];

    private float $total = 0;

    private int $amount = 0;

    /** @var Coupon[] */
    private array $coupons = [];

    public function __construct(User $user, ?int $id = null)
    {
        $this->user = $user;
        $this->id = $id;
    }

    /**
     * Add product to cart
     *
     * @param Product $product
     * @throws Exception
     * @return self
     */
    public function addProduct(Product $product): self
    {
        if(!$product->hasInventory()) {
            throw new Exception("O produto {$product->getName()} está indisponível no momento.");
        }

        $product->removeFromInventory(1);

        $this->products[] = $product;
        $this->total += $product->getPrice();
        $this->amount++;

        return $this;
    }

    /**
     * Remove product from cart
     *
     * @param Product $product
     * @return self
     */
    public function removeProduct(Product $product): self
    {
        $foundProduct = false;
        foreach($this->products as $productKey => $productCart) {
            if($productCart == $product) {
                $foundProduct = true;
                unset($this->products[$productKey]);
                $this->total -= $product->getPrice();
                $this->amount--;
                break;
            }
        }

        if(!$foundProduct) {
            throw new Exception('O produto não existe no carrinho.');
        }

        return $this;
    }

    /**
     * Add coupon to cart
     *
     * @param Coupon $coupon
     * @throws Exception
     * @return self
     */
    public function addCoupon(Coupon $coupon): self 
    {
        foreach($this->coupons as $coupon) {
            if($coupon->getCode() == $coupon->getCode()) {
                throw new Exception("O cupom {$coupon->getCode()} já foi aplicado.");
            }
        }

        $this->applyCoupon($coupon);
        return $this;
    }

    /**
     * Apply Coupon
     *
     * @param Coupon $coupon
     * @return void
     */
    public function applyCoupon(Coupon $coupon): void
    {
        $this->total -= $coupon->getValue();
        $this->coupons[] = $coupon;

        if($this->total < 0) {
            $this->total = 0;
        }
    }

    /**
     * Remove coupon
     *
     * @param Coupon $coupon
     * @return void
     */
    public function removeCoupon(Coupon $coupon): void
    {
        foreach($this->coupons as $couponKey => $couponCart) {
            if($couponCart == $coupon) {
                unset($this->coupons[$couponKey]);
                $this->total += $coupon->getValue();
                break;
            }
        }
    }

    public function getCoupons(): array
    {
        return $this->coupons;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}