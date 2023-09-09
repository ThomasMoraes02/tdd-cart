<?php 
namespace Cart\Tests\Unit\Infra;

use Cart\Model\User;
use Cart\Model\Product;
use Cart\Infra\EncoderArgon2ID;
use PHPUnit\Framework\TestCase;
use Cart\Infra\Factories\UserFactory;
use Cart\Model\Services\Mail\SendMail;
use PHPUnit\Framework\MockObject\MockObject;
use Cart\Model\Services\Mail\ProductUnavaibleMail;

class ProductUnavaibelMailTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = (new UserFactory(new EncoderArgon2ID()))
        ->create('Thomas Moraes', 'thomas@gmail.com', '123456');
    }

    /**
     * @dataProvider products
     *
     * @return void
     */
    public function testSendMailWhenProductNoHasInventory(array $products): void
    {
        /** @var MockObject|SendMail $sendMailMock */
        $sendMailMock = $this->createMock(SendMail::class);

        $sendMailMock->expects($this->exactly(3))->method('send');

        $productUnavaibleMail = new ProductUnavaibleMail($sendMailMock);
        $productUnavaibleMail->notify($this->user, $products);
    }

    /**
     * Data Provider de Produtos
     *
     * @return array
     */
    public function products(): array
    {
        $notebookDellG15 = new Product('Notebook Dell G15', 5000, 5);
        $macbook = new Product('Macbook', 7000, 0);
        $airDots = new Product('AirDots', 100, 0);
        $xiomi = new Product('Xiomi', 1000, 0);
        $iphone = new Product('Iphone', 1000, 10);

        return [
            [
                [$notebookDellG15, $macbook, $airDots, $xiomi, $iphone]
            ]
        ];
    }
}