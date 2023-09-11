<?php 
namespace Cart\Infra\Persistance;

use Cart\Model\Product\Category;
use PDO;
use Cart\Model\Product\Product;
use Cart\Model\Repository\ProductRepository;

class ProductRepositoryMysql implements ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Product $product): Product
    {
        $sql = 'INSERT INTO products (name, price, quantity) VALUES (:name, :price, :quantity)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('name', $product->getName());
        $stmt->bindValue('price', $product->getPrice());
        $stmt->bindValue('quantity', $product->getQuantity());
        $stmt->execute();

        return new Product($product->getName(), $product->getPrice(), $product->getQuantity(), $this->pdo->lastInsertId());
    }

    public function findById(int $id): ?Product
    {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $id);
        $stmt->execute();

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ? new Product($product['name'], $product['price'], $product['quantity'], $product['id']) : null;
    }

    public function update(int $id, Product $product): ?Product
    {
        $sql = "UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('name', $product->getName());
        $stmt->bindValue('price', $product->getPrice());
        $stmt->bindValue('quantity', $product->getQuantity());
        $stmt->bindValue('id', $id);

        $stmt->execute();

        return new Product($product->getName(), $product->getPrice(), $product->getQuantity(), $id);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $id);

        return $stmt->execute();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}