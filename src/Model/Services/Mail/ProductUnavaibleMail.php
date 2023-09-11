<?php 
namespace Cart\Model\Services\Mail;

use Cart\Model\User;
use Cart\Model\Product\Product;

class ProductUnavaibleMail
{
    private SendMail $sendMail;

    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

    /**
     * Notifica os usuários que o produto está indisponível
     *
     * @param Product[] $products
     * @return void
     */
    public function notify(User $user, array $products): void
    {
        foreach($products as $product) {
            if($product->hasInventory() === false) {
                $this->send($user,$product);
            }
        }
    }

    /**
     * Notifica o usuário que o produto não está mais disponível
     *
     * @param User $user
     * @param Product $product
     * @return void
     */
    public function send(User $user, Product $product): void
    {
        $message = sprintf(
            'Olá %s, o produto %s não está mais disponível. Quando o produto estiver disponível, você receberá um e-mail.',
            $user->getName(),
            $product->getName()
        );

        $this->sendMail->send(
            $user->getEmail(),
            'Produto indisponível',
            $message
        );
    }
}