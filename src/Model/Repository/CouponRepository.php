<?php 
namespace Cart\Model\Repository;

use Cart\Model\Services\Coupon\Coupon;

interface CouponRepository
{
    public function save(Coupon $coupon): ?Coupon;

    public function findCouponById(int $id): ?Coupon;

    public function findCouponByCode(string $code): ?Coupon;

    public function updateCoupon(int $id, Coupon $coupon): ?Coupon;

    public function deleteCoupon(int $id): bool;

    public function findAll(): array;
}