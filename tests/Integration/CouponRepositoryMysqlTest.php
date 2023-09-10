<?php 
namespace Cart\Tests\Integration;

use Cart\Infra\Persistance\CouponRepositoryMysql;
use Cart\Model\Repository\CouponRepository;
use PDO;
use PHPUnit\Framework\TestCase;
use Cart\Model\Services\Coupon\Coupon;
use Cart\Model\Services\Coupon\CouponTypes;

class CouponRepositoryMysqlTest extends TestCase
{
    private CouponRepository $couponRepository;

    private static PDO $pdo;

    /** @var Coupon[] */
    private array $coupons;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS coupons (id INTEGER PRIMARY KEY, code TEXT, name TEXT, value FLOAT, expiration_date DATE DEFAULT NULL, type TEXT)");
    }

    protected function setUp(): void
    {
        $coupon10OFF = new Coupon('10OFF', 'Desconto de R$10,00', 10);
        $coupon100Percent = new Coupon('100PERCENT', 'Desconto de 100%', 100);
        $coupon100Percent->configureRules(['type' => CouponTypes::PERCENTAGE]);

        $this->coupons = [$coupon10OFF, $coupon100Percent];

        self::$pdo->beginTransaction();
        $this->couponRepository = new CouponRepositoryMysql(self::$pdo);

        foreach($this->coupons as $coupon) {
            $this->couponRepository->save($coupon);
        }
    }

    public function testCreateCouponInRepository(): void
    {
        self::assertCount(2, $this->couponRepository->findAll());
    }

    public function testFindCouponByIdInRepository()
    {
        $coupon100Percent = $this->couponRepository->findCouponById(2);

        self::assertInstanceOf(Coupon::class, $coupon100Percent);
        self::assertEquals('100PERCENT', $coupon100Percent->getCode());
        self::assertEquals('Desconto de 100%', $coupon100Percent->getName());
        self::assertEquals(100, $coupon100Percent->getValue());
        self::assertEquals(CouponTypes::PERCENTAGE, $coupon100Percent->getType());
    }

    public function testFindCouponByCode(): void
    {
        $coupon10OFF = $this->couponRepository->findCouponByCode('10OFF');

        self::assertInstanceOf(Coupon::class, $coupon10OFF);
        self::assertEquals('10OFF', $coupon10OFF->getCode());
        self::assertEquals('Desconto de R$10,00', $coupon10OFF->getName());
        self::assertEquals(10, $coupon10OFF->getValue());
        self::assertEquals(CouponTypes::FIXED, $coupon10OFF->getType());
    }

    public function testUpdateCoupon(): void
    {
        $coupon5Percent = new Coupon('5PERCENT', 'Desconto de 5%', 5);
        $coupon5Percent->configureRules(['type' => CouponTypes::PERCENTAGE]);

        $coupon = $this->couponRepository->updateCoupon(2, $coupon5Percent);

        self::assertInstanceOf(Coupon::class, $coupon);
        self::assertEquals('5PERCENT', $coupon->getCode());
        self::assertEquals('Desconto de 5%', $coupon->getName());
        self::assertEquals(5, $coupon->getValue());
        self::assertEquals(CouponTypes::PERCENTAGE, $coupon->getType());
        self::assertEquals(2, $coupon->getId());
    }

    public function testDeleteCoupon(): void
    {
        $delete = $this->couponRepository->deleteCoupon(2);

        self::assertTrue($delete);
        self::assertCount(1, $this->couponRepository->findAll());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}