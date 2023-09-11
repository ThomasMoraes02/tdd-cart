<?php
namespace Cart\Infra\Persistance;

use Cart\Model\Product\Category;
use PDO;
use Cart\Model\Repository\ProductCategory;
use Cart\Model\Repository\ProductCategoryRepository;

class ProductCategoryRepositoryMysql implements ProductCategoryRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Category $category): Category
    {
        $sql = "INSERT INTO product_categories (name) VALUES (:name)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $category->getName());
        $statement->execute();

        return $this->findById($this->pdo->lastInsertId());
    }

    public function findById(int $id): ?Category
    {
        $sql = "SELECT * FROM product_categories WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id);
        
        if($statement->execute()) {
            $categoryData = $statement->fetch();

            return new Category(
                $categoryData['name'],
                $categoryData['id'],
            );
        }

        return null;
    }

    public function update(int $id, Category $category): ?Category
    {
        $sql = "UPDATE product_categories SET name = :name WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $category->getName());
        $statement->bindValue(':id', $id);
        
        if($statement->execute()) {
            return $this->findById($id);
        }

        return null;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM product_categories WHERE id = :id";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id);

        return $statement->execute();
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM product_categories";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC) ?? [];
    }
}