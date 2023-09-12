<?php 
namespace Cart\Tests\Integration\Persistance;

use Cart\Infra\Persistance\CategoryRepositoryMysql;
use PDO;
use PHPUnit\Framework\TestCase;
use Cart\Model\Product\Category;
use Cart\Model\Repository\ProductRepository;
use Cart\Model\Repository\CategoryRepository;
use Cart\Model\Repository\ProductCategoryRepository;
use Cart\Infra\Persistance\ProductCategoryRepositoryMsql;
use Cart\Infra\Persistance\ProductRepositoryMysql;
use Cart\Model\Product\Product;

class ProductCategoryRepositoryMysqlTest extends TestCase
{
    public static PDO $pdo;

    private ProductRepository $productRepository;

    private CategoryRepository $categoryRepository;

    private ProductCategoryRepository $productCategoryRepository;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite::memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY, name TEXT, price FLOAT, quantity INTEGER)");
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS categories (id INTEGER PRIMARY KEY, name TEXT)");
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS product_categories (id INTEGER PRIMARY KEY, product_id INTEGER, category_id INTEGER)");
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
        $this->productRepository = new ProductRepositoryMysql(self::$pdo);
        $this->categoryRepository = new CategoryRepositoryMysql(self::$pdo);
        $this->productCategoryRepository = new ProductCategoryRepositoryMsql(self::$pdo, $this->categoryRepository);   

        $tddComPHPUnit = new Product('TDD com PHPUnit', 27.50, 10);
        $desingPatterns = new Product('Design Patterns', 49.90, 5);
        $livroDeReceitas = new Product('Receitas da Dona Benta');
        $cleanArchitecture = new Product('Clean Architecture', 129.90, 12);

        $categories = ['Programação', 'PHP', 'Testes', 'Receitas', 'Arquitetura'];
        $books = [$tddComPHPUnit, $desingPatterns, $livroDeReceitas, $cleanArchitecture];

        foreach($categories as $category) {
            $this->categoryRepository->save(new Category($category));
        }

        foreach($books as $book) {
            $this->productRepository->save($book);
        }
    }

    public function testSaveProductCategoriesInRepository()
    {
        $tddComPHPUnit = $this->productRepository->findById(1);
        $tddComPHPUnit
        ->addCategory($this->categoryRepository->findById(1))
        ->addCategory($this->categoryRepository->findById(2))
        ->addCategory($this->categoryRepository->findById(3));

        $tddComPHPUnit = $this->productCategoryRepository->save($tddComPHPUnit);

        self::assertEquals('Programação', $tddComPHPUnit->getCategories()[0]->getName());
        self::assertEquals(1, $tddComPHPUnit->getCategories()[0]->getId());
        self::assertCount(5, $this->categoryRepository->findAll());
        self::assertCount(4, $this->productRepository->getAll());
    }

    public function testFindAllProductsByCategory()
    {
        $tddComPHPUnit = $this->productRepository->findById(1);
        $tddComPHPUnit->addCategory($this->categoryRepository->findById(1));
        $tddComPHPUnit = $this->productCategoryRepository->save($tddComPHPUnit);

        $desingPatterns = $this->productRepository->findById(2);
        $desingPatterns->addCategory($this->categoryRepository->findById(1));
        $desingPatterns = $this->productCategoryRepository->save($desingPatterns);

        $livroDeReceitas = $this->productRepository->findById(3);
        $livroDeReceitas->addCategory($this->categoryRepository->findById(4));
        $livroDeReceitas = $this->productCategoryRepository->save($livroDeReceitas);

        $cleanArchitecture = $this->productRepository->findById(4);
        $cleanArchitecture->addCategory($this->categoryRepository->findById(5));
        $cleanArchitecture = $this->productCategoryRepository->save($cleanArchitecture);

        self::assertCount(2, $this->productCategoryRepository->findAllProductsByCategory($this->categoryRepository->findById(1)));
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}