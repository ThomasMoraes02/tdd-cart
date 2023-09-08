<?php 
namespace Cart\Model\Repository;

use Cart\Model\Product;

interface ProductRepository
{
    public function save(Product $product): ?Product;

    public function findById(int $id): ?Product;

    public function update(int $id, Product $product): ?Product;

    public function delete(int $id): bool;

    public function getAll(): array;
}