<?php 
namespace Cart\Model\Repository;

use Cart\Model\Product\Category;

interface CategoryRepository
{
    public function save(Category $category): Category;

    public function findById(int $id): ?Category;

    public function update(int $id, Category $category): ?Category;

    public function delete(int $id): bool;

    public function findAll(): array;
}