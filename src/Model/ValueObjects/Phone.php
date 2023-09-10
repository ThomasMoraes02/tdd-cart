<?php 
namespace Cart\Model\ValueObjects;

class Phone
{
    private ?int $id;

    private int $areaCode;

    private int $number;

    public function __construct(int $areaCode, int $number, ?int $id = null)
    {
        $this->areaCode = trim($areaCode);
        $this->number = trim($number);
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAreaCode(): int
    {
        return $this->areaCode;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function __toString(): string
    {
        return sprintf('%s%s', $this->areaCode, $this->number);
    }
}