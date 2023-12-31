<?php 
namespace Cart\Model\Product;

use Exception;
use Cart\Model\Product\Category;

class Product
{
    private ?int $id;

    private string $name;

    private float $price;

    private int $quantity;

    /** @var Category[] */
    private array $categories;

    public function __construct(string $name, float $price = 0, int $quantity = 1, ?int $id = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;

        $this->categories = [];
        $this->hasInventory();
        $this->id = $id;
    }

    public function addCategory(Category $category): self
    {
        $this->categories[] = $category;
        return $this;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function hasInventory(): bool
    {
        return ($this->quantity >= 1) ? true : false;
    }

    /**
     * @param integer $quantity
     * @throws Exception
     * @return void
     */
    public function removeFromInventory(int $quantity): void
    {
        $this->quantity -= $quantity;
        $this->quantity = max(0, $this->quantity);
    }

    /**
     * @param integer $quantity
     * @return void
     */
    public function addToInventory(int $quantity): void
    {
        $this->quantity += $quantity;
    }
}