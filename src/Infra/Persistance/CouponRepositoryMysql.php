<?php 
namespace Cart\Infra\Persistance;

use PDO;
use Cart\Model\Repository\CouponRepository;
use Cart\Model\Services\Coupon\Coupon;

class CouponRepositoryMysql implements CouponRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Coupon $coupon): ?Coupon
    {
        $sql = "INSERT INTO coupons (code, name, value, expiration_date, type) VALUES (:code, :name, :value, :expiration_date, :type)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $coupon->getCode());
        $stmt->bindValue(':name', $coupon->getName());
        $stmt->bindValue(':value', $coupon->getValue());
        $stmt->bindValue(':expiration_date', $coupon->getExpirationDate());
        $stmt->bindValue(':type', $coupon->getType());

        if($stmt->execute()) {
            return $this->findCouponById($this->pdo->lastInsertId());
        }

        return null;
    }

    public function findCouponById(int $id): ?Coupon
    {
        $ql = "SELECT * FROM coupons WHERE id = :id";
        $stmt = $this->pdo->prepare($ql);
        $stmt->bindValue('id', $id);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            $coupon = new Coupon($result['code'], $result['name'], $result['value'],$result['id']);
            $coupon->configureRules([
                'expiration_date' => $result['expiration_date'],
                'type' => $result['type']
            ]);

            return $coupon;
        }

        return null;
    }

    public function findCouponByCode(string $code): ?Coupon
    {
        $ql = "SELECT * FROM coupons WHERE code = :code";
        $stmt = $this->pdo->prepare($ql);
        $stmt->bindValue('code', $code);

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result) {
            return $this->findCouponById($result['id']);
        }

        return null;
    }

    public function updateCoupon(int $id, Coupon $coupon): ?Coupon
    {
        $sql = "UPDATE coupons SET code = :code, name = :name, value = :value, expiration_date = :expiration_date, type = :type WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('code', $coupon->getCode());
        $stmt->bindValue('name', $coupon->getName());
        $stmt->bindValue('value', $coupon->getValue());
        $stmt->bindValue('expiration_date', $coupon->getExpirationDate());
        $stmt->bindValue('type', $coupon->getType());
        $stmt->bindValue('id', $id);

        if($stmt->execute()) {
            return $this->findCouponById($id);
        }

        return null;
    }

    public function deleteCoupon(int $id): bool
    {
        $sql = "DELETE FROM coupons WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $id);

        return $stmt->execute();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM coupons";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}