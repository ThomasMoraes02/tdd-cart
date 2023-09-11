<?php 
namespace Cart\Infra\Persistance;

use PDO;
use Cart\Model\Product\Product;
use Cart\Model\Product\Category;
use Cart\Model\Repository\CategoryRepository;
use Cart\Model\Repository\ProductCategoryRepository;

class ProductCategoryRepositoryMsql implements ProductCategoryRepository
{
    private PDO $pdo;

    private CategoryRepository $categoryRepository;

    public function __construct(PDO $pdo, CategoryRepository $categoryRepository)
    {
        $this->pdo = $pdo;
        $this->categoryRepository = $categoryRepository;
    }

    public function save(Product $product): Product
    {
        $sql = 'INSERT INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':product_id', $product->getId(), PDO::PARAM_INT);

        foreach($product->getCategories() as $category) {
            $stmt->bindValue(':category_id', $category->getId(), PDO::PARAM_INT);
            $stmt->execute();
        }

        $categories = $this->findAllCategoriesByProduct($product);

        $product = new Product(
            $product->getName(), 
            $product->getPrice(), 
            $product->getQuantity(), 
            $product->getId()
        );

        foreach($categories as $categoryId) {
            $category = $this->categoryRepository->findById($categoryId['id']);
            if($category) {
                $product->addCategory($category);
            }
        }

        return $product;
    }

    public function findAllCategoriesByProduct(Product $product): array
    {
        $sql = 'SELECT * FROM product_categories WHERE product_id = :product_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':product_id', $product->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllProductsByCategory(Category $category): array
    {
        $sql = 'SELECT * FROM product_categories WHERE category_id = :category_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $category->getId(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}