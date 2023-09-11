<?php 
namespace Cart\Tests\Integration;

use Cart\Infra\Persistance\ProductCategoryRepositoryMysql;
use Cart\Model\Product\Category;
use PDO;
use PHPUnit\Framework\TestCase;
use Cart\Model\Repository\ProductCategoryRepository;

class ProductCategoryMysqlTest extends TestCase
{
    private static PDO $pdo;

    private ProductCategoryRepository $productCategoryRepository;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS product_categories (id INTEGER PRIMARY KEY, name TEXT)");
    }

    /**
     * @dataProvider categories
     * */
    public function setUp(): void
    {
        $this->productCategoryRepository = new ProductCategoryRepositoryMysql(self::$pdo);
        self::$pdo->beginTransaction();

        $this->productCategoryRepository->save(new Category('Livros'));
        $this->productCategoryRepository->save(new Category('Programação'));
        $this->productCategoryRepository->save(new Category('PHP'));
        $this->productCategoryRepository->save(new Category('Testes'));
    }

    public function testSaveCategoriesInRepository(): void
    {
        $categories = $this->productCategoryRepository->findAll();
        self::assertCount(4, $categories);
    }

    public function testDeleteCategoriesInRepository(): void
    {
        $this->productCategoryRepository->delete(1);
        $this->productCategoryRepository->delete(3);
        $this->productCategoryRepository->delete(4);

        $categories = $this->productCategoryRepository->findAll();

        self::assertCount(1, $categories);
    }

    public function testUpdateCategoryInRepository()
    {
        $php8 = new Category('PHP 8');
        $category = $this->productCategoryRepository->update(3, $php8);

        self::assertEquals('PHP 8', $category->getName());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }   
}