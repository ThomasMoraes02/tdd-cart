<?php 
namespace Cart\Tests\Integration\Persistance;

use Cart\Infra\Persistance\CategoryRepositoryMysql;
use Cart\Model\Product\Category;
use Cart\Model\Repository\CategoryRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class CategoryRepositoryMysqlTest extends TestCase
{
    private static PDO $pdo;

    private CategoryRepository $categoryRepository;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY, name TEXT)");
    }

    /**
     * @dataProvider categories
     * */
    public function setUp(): void
    {
        $this->categoryRepository = new CategoryRepositoryMysql(self::$pdo);
        self::$pdo->beginTransaction();

        $this->categoryRepository->save(new Category('Livros'));
        $this->categoryRepository->save(new Category('Programação'));
        $this->categoryRepository->save(new Category('PHP'));
        $this->categoryRepository->save(new Category('Testes'));
    }

    public function testSaveCategoriesInRepository(): void
    {
        $categories = $this->categoryRepository->findAll();
        self::assertCount(4, $categories);
    }

    public function testDeleteCategoriesInRepository(): void
    {
        $this->categoryRepository->delete(1);
        $this->categoryRepository->delete(3);
        $this->categoryRepository->delete(4);

        $categories = $this->categoryRepository->findAll();

        self::assertCount(1, $categories);
    }

    public function testUpdateCategoryInRepository()
    {
        $php8 = new Category('PHP 8');
        $category = $this->categoryRepository->update(3, $php8);

        self::assertEquals('PHP 8', $category->getName());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }   
}