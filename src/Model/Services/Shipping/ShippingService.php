<?php 
namespace Cart\Model\Services\Shipping;

use Cart\Model\Cart;
use Cart\Model\ValueObjects\Address;

interface ShippingService
{
    public function calculateShipping(Cart $cart, Address $address): array;

    public function getShippingCost(): float;
}