<?php 
namespace Cart\Model\Services\Coupon;

class Coupon
{
    private ?int $id;

    private string $code;

    private string $name;

    private float $value;

    public function __construct(string $code, string $name, float $value, ?int $id = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;   
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}