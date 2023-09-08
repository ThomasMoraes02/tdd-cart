<?php 
namespace Cart\Model\Services\Coupon;

use DateTimeInterface;
use Cart\Model\Services\Coupon\CouponTypes;

class Coupon
{
    private ?int $id;

    private string $code;

    private string $name;

    private float $value;

    private ?DateTimeInterface $expirationDate;

    private string $type;

    public function __construct(string $code, string $name, float $value, ?int $id = null)
    {
        $this->code = $code;
        $this->name = $name;
        $this->value = $value;
        $this->expirationDate = null;
        $this->type = CouponTypes::FIXED;
        $this->id = $id;   
    }

    /**
     * Configure the coupon
     *
     * @param array $rules
     * @return void
     */
    public function configureRules(array $rules): void
    {
        $this->expirationDate = $rules['expiration_date'] ?? null;
        $this->type = $rules['type'] ?? CouponTypes::FIXED;
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

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function getType(): string
    {
        return $this->type;
    }
}