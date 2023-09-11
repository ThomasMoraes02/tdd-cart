<?php 
namespace Cart\Model\ValueObjects;

class Address
{
    private ?int $id;

    private string $zipCode;

    private string $street;

    private string $number;

    private ?string $complement;

    private string $city;

    private string $state;

    private string $country;

    public function __construct(string $zipCode, string $street, string $number, string $city, string $state, string $country, ?string $complement = null, ?int $id = null)
    {
        $this->zipCode = $zipCode;
        $this->street = $street;
        $this->number = $number;
        $this->city = $city;
        $this->state = $state;
        $this->country = $country;
        $this->complement = $complement;
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}