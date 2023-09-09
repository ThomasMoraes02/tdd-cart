<?php 
namespace Cart\Tests\Integration;

use PDO;
use PHPUnit\Framework\TestCase;
use Cart\Model\Repository\ProductRepository;
use Cart\Infra\Persistance\ProductRepositoryMysql;
use Cart\Model\Product;

class ProductRepositoryMysqlTest extends TestCase
{
    private static PDO $pdo;

    private ProductRepository $productRepository;

    /**
     * É executando antes de iniciar a bateria de testes
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::$pdo = new PDO('sqlite:memory:');
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY, name TEXT, price FLOAT, quantity INTEGER)");
    }

    /**
     * Iniciado a cada teste
     *
     * @return void
     */
    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
        $this->productRepository = new ProductRepositoryMysql(self::$pdo);
    }

    /**
     * @dataProvider products
     *
     * @param array $products
     * @return void
     */
    public function testCreateProductInRepository(array $products): void
    {
        foreach($products as $product){
            $this->productRepository->save($product);
        }

        $products = $this->productRepository->getAll();

        self::assertCount(3, $products);

        self::assertEquals('Notebook Dell G15', $products[0]['name']);
        self::assertEquals(5200, $products[0]['price']);
        self::assertEquals(5, $products[0]['quantity']);
    }

    /**
     * @dataProvider products
     *
     * @param array $products
     * @return void
     */
    public function testUpdateProductInRepository(array $products): void
    {
        $product = new Product('AirDots 3', 150, 5);
        $product = $this->productRepository->update(3, $product);

        self::assertEquals('AirDots 3', $product->getName());
        self::assertEquals(150, $product->getPrice());
        self::assertEquals(5, $product->getQuantity());
        self::assertTrue($product->hasInventory());
    }

    /**
     * @dataProvider products
     *
     * @param array $products
     * @return void
     */
    public function testDeleteProductInRepository(array $products): void
    {
        foreach($products as $product){
            $this->productRepository->save($product);
        }

        $this->productRepository->delete(1);

        self::assertCount(2, $this->productRepository->getAll());
    }

    /**
     * Data Provider de Produtos
     *
     * @return array
     */
    public function products(): array
    {
        $notebook = new Product('Notebook Dell G15', 5200, 5);
        $xiomi = new Product('Xiomi Redmi Note 10', 3000, 10);
        $airDots = new Product('AirDots', 100, 0);

        return [
            [
                [$notebook, $xiomi, $airDots]
            ]
        ];
    }

    /**
     * Excutado ao final de cada teste
     *
     * @return void
     */
    protected function tearDown(): void
    {
        self::$pdo->rollBack();   
    }
}