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

    /**
     * Atualiza o Produto
     *
     * @param array $data
     * @return Product|null
     */
    public function update(array $data): ?Product
    {
        $product = $this->productRepository->findById($data['id'] ?? '');

        if(!$product) {
            return null;
        }

        $product = new Product(
            $data['name'] ?? $product->getName(),
            $data['price'] ?? $product->getPrice(),
            $data['quantity'] ?? $product->getQuantity(),
            $product->getId()
        );

        $product = $this->productRepository->update($product->getId(), $product);
        $this->deleteCategories($product);

        if(!empty($data['category_ids'])) {
            $product = $this->updateCategories($product, $data['category_ids']);
        }

        return $product;
    }

    /**
     * Deleta as categorias do Produto
     *
     * @param Product $product
     * @return void
     */
    private function deleteCategories(Product $product): bool
    {
        $categories = $this->productCategoryRepository->findAllCategoriesByProduct($product);

        if(!empty($categories)) {
            return $this->productCategoryRepository->deleteAllCategoriesByProduct($product);
        }

        return true;
    }

    /**
     * Atualiza as categorias do Produto
     *
     * @param Product $product
     * @param array $category_ids
     * @return Product
     */
    private function updateCategories(Product $product, array $category_ids): Product
    {
        foreach($category_ids as $category_id) {
            $category = $this->categoryRepository->findById($category_id);
            if($category) {
                $product->addCategory($category);
            }
        }

        if(!empty($product->getCategories())) {
            $product = $this->productCategoryRepository->save($product);
        }

        return $product;
    }

    /**
     * Delete o Produto e as suas categorias
     *
     * @param integer $id
     * @return boolean
     */
    public function delete(int $id): bool
    {
        $product = $this->productRepository->findById($id);

        if(!$product) {
            return false;
        }

        $this->deleteCategories($product);

        return $this->productRepository->delete($id);
    }
}