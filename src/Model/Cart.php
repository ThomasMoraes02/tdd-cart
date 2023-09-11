<?php 
namespace Cart\Model;

use Exception;
use Cart\Model\User;
use Cart\Model\Product\Product;
use Cart\Model\Services\Coupon\Coupon;
use Cart\Model\Services\Coupon\CouponManager;
use Cart\Model\Services\Shipping\ShippingService;

class Cart
{
    private ?int $id;

    private User $user;

    /** @var Product[] */
    private array $products = [];

    private float $total = 0;

    private float $subtotal = 0;

    private int $amount = 0;

    private ?ShippingService $shipping;

    private CouponManager $couponManager;

    public function __construct(User $user, ?ShippingService $shipping = null, ?int $id = null)
    {
        $this->user = $user;
        $this->shipping = $shipping;
        $this->id = $id;

        $this->couponManager = new CouponManager($this);
    }

    /**
     * Add product to cart
     *
     * @param Product $product
     * @param int $quantity
     * @throws Exception
     * @return self
     */
    public function addProduct(Product $product, int $quantity = 1): self
    {
        if(!$product->hasInventory()) {
            throw new Exception("O produto {$product->getName()} está indisponível no momento.");
        }

        if($product->getQuantity() < $quantity) {
            throw new Exception("O produto {$product->getName()} possui apenas {$product->getQuantity()} unidades.");
        }

        $product->removeFromInventory($quantity);

        $this->products[] = $product;
        $this->total += ($product->getPrice() * $quantity);
        $this->subtotal += ($product->getPrice() * $quantity);
        $this->amount++;

        return $this;
    }

    /**
     * Remove product from cart
     *
     * @param Product $product
     * @param int $quantity
     * @return self
     */
    public function removeProduct(Product $product, int $quantity = 1): self
    {
        $foundProduct = false;
        foreach($this->products as $productKey => $productCart) {
            if($productCart == $product) {
                $foundProduct = true;

                $productQuantity = ($quantity > $product->getQuantity()) ? ($quantity - $product->getQuantity()) : $quantity;
                $product->addToInventory($productQuantity);

                if($quantity > $productCart->getQuantity()) {
                    unset($this->products[$productKey]);
                }

                $this->total -= ($product->getPrice() * $quantity);
                $this->subtotal -= ($product->getPrice() * $quantity);
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
    public function addCoupon(Coupon $coupon): void 
    {
        $this->total = max(0, $this->couponManager->applyCoupon($coupon));
    }

    /**
     * Remove coupon
     *
     * @param Coupon $coupon
     * @return void
     */
    public function removeCoupon(Coupon $coupon): void
    {
        $this->total = max(0, $this->couponManager->removeCoupon($coupon));
    }

    public function getCoupons(): array
    {
        return $this->couponManager->getCoupons();
    }

    public function getShipping(): ShippingService
    {
        return $this->shipping;
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

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}