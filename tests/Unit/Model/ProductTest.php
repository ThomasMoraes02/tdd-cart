<?php 
namespace Cart\Tests\Unit\Model;

use Cart\Model\Product\Category;
use Exception;
use Cart\Model\Product\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product('Notebook Dell G15', 4500.50, 3);
    }

    public function testCreateProduct(): void
    {
        self::assertEquals('Notebook Dell G15', $this->product->getName());
        self::assertEquals(4500.50, $this->product->getPrice());
        self::assertEquals(3, $this->product->getQuantity());
        self::assertTrue($this->product->hasInventory());
        self::assertNull($this->product->getId());
    }

    public function testRemoveFromInventory(): void
    {
        $this->product->removeFromInventory(2);
        self::assertEquals(1, $this->product->getQuantity());
        self::assertTrue($this->product->hasInventory());
    }

    public function testZeroInventory()
    {
        $this->product->removeFromInventory(4);
        self::assertEquals(0, $this->product->getQuantity());
    }

    public function testAddToInventory()
    {
        $this->product->addToInventory(6);
        self::assertEquals(9, $this->product->getQuantity());
    }

    public function testCreateProductWithCategories()
    {
        $product = new Product('TDD Testes com PHP Unit', 27.80, 100);
        $product
        ->addCategory(new Category('Livros'))
        ->addCategory(new Category('Programação'))
        ->addCategory(new Category('PHP'))
        ->addCategory(new Category('Testes'));

        self::assertEquals('Livros', $product->getCategories()[0]->getName());
        self::assertCount(4, $product->getCategories());
    }
}