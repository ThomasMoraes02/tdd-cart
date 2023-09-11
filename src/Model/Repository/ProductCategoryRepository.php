<?php 
namespace Cart\Model\Repository;

use Cart\Model\Product\Category;
use Cart\Model\Product\Product;

interface ProductCategoryRepository
{
    public function save(Product $product): Product;

    public function findAllCategoriesByProduct(Product $product): array;

    public function findAllProductsByCategory(Category $category): array;
}