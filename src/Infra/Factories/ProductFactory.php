<?php 
namespace Cart\Infra\Factories;

use Cart\Model\Product\Product;
use Cart\Model\Repository\CategoryRepository;
use Cart\Model\Repository\ProductCategoryRepository;
use Cart\Model\Repository\ProductRepository;

class ProductFactory
{
    private ProductRepository $productRepository;

    private CategoryRepository $categoryRepository;

    private ProductCategoryRepository $productCategoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository, ProductCategoryRepository $productCategoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * Fabrica um Produto
     *
     * @param array $data
     * @return Product|null
     */
    public function create(array $data): ?Product
    {
        $product = new Product(
            $data['name'],
            $data['price'],
            $data['quantity'],
            $data['id'] ?? null
        );

        $product = $this->productRepository->save($product);

        if($product) {
            if(!empty($data['category_ids'])) {
                foreach($data['category_ids'] as $category_id) {
                    $category = $this->categoryRepository->findById($category_id);
                    if($category) {
                        $product->addCategory($category);
                    }
                }

                $product = $this->productCategoryRepository->save($product);
            }

            return $product;
        }
        
        return null;
    }
}