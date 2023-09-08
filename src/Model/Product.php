<?php 
namespace Cart\Model;

use Exception;

class Product
{
    private ?int $id;

    private string $name;

    private float $price;

    private int $quantity;

    public function __construct(string $name, float $price = 0, int $quantity = 1, ?int $id = null)
    {
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;

        $this->hasInventory();
        $this->id = $id;
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

        if($this->quantity < 0) {
            throw new Exception('O produto não possui mais estoque disponível');
        }
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