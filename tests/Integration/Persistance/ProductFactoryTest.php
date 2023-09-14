<?php 
namespace Cart\Tests\Integration\Persistance;

use Cart\Infra\Factories\ProductFactory;
use PDO;
use Cart\Model\Product\Product;
use PHPUnit\Framework\TestCase;
use Cart\Model\Product\Category;
use Cart\Model\Repository\ProductRepository;
use Cart\Model\Repository\CategoryRepository;
use Cart\Infra\Persistance\ProductRepositoryMysql;
use Cart\Infra\Persistance\CategoryRepositoryMysql;
use Cart\Model\Repository\ProductCategoryRepository;
use Cart\Infra\Persistance\ProductCategoryRepositoryMsql;

use function PHPUnit\Framework\assertTrue;

class ProductFactoryTest extends TestCase
{
    public static PDO $pdo;

    private ProductFactory $productFactory;

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

        $this->productFactory = new ProductFactory($this->productRepository, $this->categoryRepository, $this->productCategoryRepository);

        $categories = ['Programação', 'PHP', 'Testes', 'Receitas', 'Arquitetura'];

        foreach($categories as $category) {
            $this->categoryRepository->save(new Category($category));
        }
    }

    /**
     * @dataProvider product
     *
     * @param array $product
     * @return void
     */
    public function testCreateProductInFactory(array $productData): void
    {
        $product = $this->productFactory->create($productData);

        self::assertInstanceOf(Product::class, $product);
        self::assertCount(3, $product->getCategories());
        self::assertEquals('Testes', $product->getCategories()[2]->getName());
        self::assertEquals(3, $product->getCategories()[2]->getId());
    }

    /**
     * @dataProvider product
     *
     * @param array $product
     * @return void
     */
    public function testCreateProductNoHasCategory(array $productData): void
    {
        unset($productData['category_ids']);
        $product = $this->productFactory->create($productData);

        self::assertEmpty($product->getCategories());
    }

    /**
     * @dataProvider product
     *
     * @param array $productData
     * @return void
     */
    public function testUpdateProductInFactory(array $productData): void
    {
        $product = $this->productFactory->create($productData); 

        $productData = [
            'id' => $product->getId(),
            'name' => 'Arquitetura Limpa',
            'price' => 129.9,
            'quantity' => 1,
            'category_ids' => [1, 5]
        ];

        $product = $this->productFactory->update($productData);

        self::assertInstanceOf(Product::class, $product);
        self::assertEquals('Arquitetura Limpa', $product->getName());
        self::assertEquals(129.9, $product->getPrice());
        self::assertEquals(1, $product->getQuantity());
        self::assertCount(2, $product->getCategories());
        self::assertEquals('Programação', $product->getCategories()[0]->getName());
        self::assertEquals('Arquitetura', $product->getCategories()[1]->getName());
    }

    /**
     * @dataProvider product
     *
     * @param array $productData
     * @return void
     */
    public function testUpdateProductNoHasCategoryInFactory(array $productData): void
    {
        $product = $this->productFactory->create($productData); 

        $productData = [
            'id' => $product->getId(),
            'name' => 'Arquitetura Limpa',
            'price' => 129.9,
            'quantity' => 1
        ];

        $product = $this->productFactory->update($productData);

        self::assertEmpty($product->getCategories());
    }

    /**
     * @dataProvider product
     *
     * @param array $productData
     * @return void
     */
    public function testDeleteProductInFactory(array $productData): void
    {
        $product = $this->productFactory->create($productData);

        assertTrue($this->productFactory->delete($product->getId()));
    }

    /**
     * @dataProvider product
     *
     * @param array $productData
     * @return void
     */
    public function testLoadProductInFactory(array $productData): void
    {
        $productSaved = $this->productFactory->create($productData);

        $product = $this->productFactory->load($productSaved->getId());

        self::assertInstanceOf(Product::class, $product);
        self::assertNotEmpty($product->getCategories());
    }

    public function product(): array
    {
        return [
            [
                [
                    'name' => 'TDD: Testes com PHP Unit',
                    'price' => 39.9,
                    'quantity' => 10,
                    'category_ids' => [1, 2, 3]
                ]
            ]
        ];
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}